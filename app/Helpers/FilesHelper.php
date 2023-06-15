<?php

namespace App\Helpers;

use App\Http\Resources\PostsResource;
use App\Models\BotUsers;
use App\Models\Posts;
use DateTime;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;

trait FilesHelper {

    static function KbTobB( $kp = 0 ) {
        return $kp / ( 1024 * 1024 );
    }

    static function RandomString( $len = 5, $upper = true ) {
        $str = Str::random( $len );
        if ( $upper ) return Str::upper( $str );
        return $str;
    }

    static function DownloadMedia( $url, $path ) {
        try {
            file_put_contents( $path, file_get_contents( $url ) );
        } catch ( \Throwable $th ) {
            return 'Failed: '.$th->getMessage();
        }
    }

    static function MakeTitle( $caption, $path ) {
        $caption = Str::upper( $caption );
        $title = null;
        if ( strstr( $caption, 'TITLE' ) ) {
            $extract = explode( 'TITLE', $caption??'' )[ 1 ];
            $title = explode( "/,\s*$/m", $extract??'' )[ 0 ];
        }
        return $title?? 'Untitied Video';
    }

    static function SaveUser( Nutgram $bot ) {
        $message = $bot->message();
        $chat  = $bot->chat();
        $from = $message?->from ?? $bot?->inlineQuery()?->from;

        $findUser = BotUsers::where( 'username', $from?->username )->orWhere( 'chat_id', $from?->id )->first();
        $user = $findUser;

        if ( $bot->isInlineQuery() ) {
            $user[ 'user_type' ] = 'isInlineQuery';
            // Log::channel( 'telegram' )->alert( '', [ $bot->inlineQuery() ] );
        }

        if ( $chat?->isChannel() ) {
            $user[ 'user_type' ] = 'isChannel';
            // Log::channel( 'telegram' )->alert( '', [ 'isChannel' ] );
        }
        if ( $chat?->isGroup() ) {
            $user[ 'user_type' ] = 'isGroup';
            // Log::channel( 'telegram' )->alert( '', [ 'isGroup' ] );

        }
        if ( $chat?->isSupergroup() ) {
            $user[ 'user_type' ] = 'isSuperGroup';
            $from = $message->from;
            // Log::channel( 'telegram' )->alert( '', [ 'isSupergroup' ] );
        }

        if ( $chat?->isPrivate() ) {
            $user[ 'user_type' ] = 'isPrivate';
            // Log::channel( 'telegram' )->alert( '', [ 'isPrivate' ] );
        }

        if ( $from ) {
            $user = self::MakeUser( $from, $user, $findUser );
            $user = is_array( $user ) ? $user : $user->toArray();
            if ( $findUser ) return $findUser->save();
            BotUsers::create( $user );
        }

    }

    static function MakeUser( $from, $user = [], $oldUser = null ) {
        $user[ 'username' ] = $from->username;
        $user[ 'first_name' ] = $from->first_name;
        $user[ 'last_name' ] = $from->last_name;
        $user[ 'chat_id' ] = $from->id;
        $user[ 'last_checkin' ] = time();
        $user[ 'role' ] = [ 'user' ];
        $user[ 'query_count' ] = ( $user[ 'query_count' ]?? 0 )  +1;
        if ( $oldUser ) $oldUser = $user;
        return $oldUser ?? $user;
    }

    static function  WebAppUrl( $target = '' ) {
        return config( 'app.web_app_url' ).$target;
    }

    static function ThumbUrl( $target = '' ) {
        return config( 'app.url' ).$target;
    }

    static function WatchUrl( $target = '' ) {
        return config( 'app.view_wesite' ).$target;
    }

    static function SaveFile( $filez ) {
        $post_id = Str::random( 5 );
        $linkToPost = config( 'app.wesite_url' ).explode( '/', $filez[ 'mime_type' ] )[ 0 ].'?v='.$post_id;
        $thumbnailPath = "uploads/thumbs/$post_id".'.'.pathinfo( $filez[ 'thumbnail' ], PATHINFO_EXTENSION );
        try {
            self::DownloadMedia( $filez[ 'thumbnail' ], public_path( $thumbnailPath ) );
            // $bot::DownloadMedia( $vur, public_path( 'uploads/videos/'.$post_id.'.'.pathinfo( $vur, PATHINFO_EXTENSION ) ) );
        } catch ( \Throwable $th ) {
            Log::channel( 'telegram' )->info( '', [ $th->getMessage() ] );
        }
        $saved = Posts::create( [
            'file_id'=>$post_id,
            'file_type'=> $filez[ 'mime_type' ],
            'file_caption'=> $filez[ 'caption' ] ?? 'No Caption',
            'file_size'=> self::KbTobB( $filez[ 'file_size' ] ),
            'file_uploader'=> $filez[ 'username' ],
            'file_views'=>0,
            'file_downloads'=>0,
            'file_parent_path'=> $filez[ 'parent_path' ],
            'file_description'=>'',
            'file_remote_id'=> $filez[ 'file_id' ],
            'file_thumbnails'=>   $thumbnailPath,
            'file_download_link'=> $linkToPost,
        ] );
        return [ 'link' => $linkToPost, 'post_id'=>$post_id ];
    }
}