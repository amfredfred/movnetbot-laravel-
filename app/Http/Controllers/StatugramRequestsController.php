<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResponseResource;
use App\Http\Resources\PostsResource;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatugramRequestsController extends Controller
 {
    function watch( Request $request ) {
        try {
            $video = Posts::where( 'file_id', $request->input( 'v' ) )->first();
            $post = new PostResponseResource( $video );
            Log::channel( 'telegram' )->emergency( 'SERVER', [ 'message' => 'Video REquest' ] );
            if ( $post ) return response()->json( $post, 200 );
            return response()->json( 'Video no found', 404 );
        } catch ( \Throwable $th ) {
            Log::channel( 'telegram' )->emergency( 'SERVER', [ 'message' => $th->getMessage() ] );
            return response()->json( [ 'message' => $th->getMessage() ], 200 );
        }
    }
}
