<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResponseResource;
use App\Models\BotUsers;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use ZipArchive;

class StatugramRequestsController extends Controller
 {
    public function watch( Request $request ) {
        try {
            $video = Posts::where( 'file_id', $request->input( 'v' ) )->first();
            $post = new PostResponseResource( $video );
            if ( $post ) return response()->json( $post, 200 );
            return response()->json( 'Video not found', 404 );
        } catch ( \Throwable $th ) {
            Log::channel( 'telegram' )->emergency( 'SERVER', [ 'message' => $th->getMessage() ] );
            return response()->json( [ 'message' => $th->getMessage() ], 500 );
        }
    }

    public function download( Request $request ) {

        $method = $request->input( 'method' );
        $compressed = $request->input( 'compressed' );
        $link = $request->input( 'link' );
        $username = $request->input( 'username' );
        $uid = $request->input( 'uid' );
        $bot  = new Nutgram( config( 'nutgram.token' ) );
        $response = 'sent...';
        $post = Posts::where( 'file_id', $uid )->orWhere( 'id', $uid )->first();
        try {
            $user = BotUsers::where( 'username', $username )->first();
            $chatid = $user?->chat_id;
            if ( $chatid ) {
                $bot->sendMessage( "Hey {$user?->first_name}, I got your request!!! \n\nHold on 😍😌", chat_id:$chatid );
                $document_path = public_path( $post->file_path );
                $document = fopen( $document_path, 'r+' );
                if ( $post?->file_id ) {
                    if ( $compressed === 'document' ) {
                        if ( $method === 'web' ) {
                            // $response =  response()->download( $document );

                        } else if ( $method === 'telegram' ) {
                            $bot->sendDocument(
                                InputFile::make( $document ),
                                caption: $post?->file_caption,
                                chat_id:$chatid
                            );
                            $response = 'File sent...';
                        }
                    } else if ( $compressed === 'zip' ) {
                        $zipper = new ZipArchive();
                        $new_zipped_file = public_path( "temp\\zipped-{$bot::MakeSlug( $post->file_caption)}.zip" );
                        if ( $zipper->open( $new_zipped_file, ZipArchive::CREATE ) === true ) {
                            $zipper->addFile( $document_path, $bot::MakeSlug( $post->file_caption ).'.'.pathinfo( $document_path, PATHINFO_EXTENSION ) );
                            $zipper->close();
                            if ( $method === 'telegram' ) {
                                $bot->sendDocument(
                                    InputFile::make( fopen( $new_zipped_file, 'r+' ) ),
                                    caption:$post->file_caption,
                                    chat_id:$chatid
                                );
                                $response = 'Sent to telegram...';
                            } else if ( $method === 'web' ) {
                                // $response =  response()->download( $new_zipped_file );

                            }
                            unlink( $new_zipped_file );
                        } else {
                            // could not open file to write
                        }
                    }
                } else {
                    // file may not exist
                }
            } else {
                // chat may not exist
            }
            $post->file_downloads += 1;
            $post->save();
        } catch ( \Throwable $th ) {
            $response = $th->getMessage();
            $bot->sendMessage( "Hey {$user?->first_name}, something went wrong, Try Again! 😢😢", chat_id:$chatid );
            Log::channel( 'telegram' )->error( 'SEND_ERROR', [ $response ] );
            return response()->json( $response, 500 );
        }
        return response()->json( $response, 200 );
    }
}
