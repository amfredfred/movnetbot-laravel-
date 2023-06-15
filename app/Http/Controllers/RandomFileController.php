<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResponseResource;
use Illuminate\Http\Request;
use App\Models\Posts;
use Illuminate\Support\Facades\Log;

class RandomFileController extends Controller
 {
    public function random( Request $request ) {
        $post = null;
        $method = $request->json( 'method' );
        $currentId = $request->json( 'current_id' );

        try {
            switch ( $method ) {
                case 'next':
                $post = Posts::where( 'id', $currentId+1 )->first();
                break;
                case 'previous':
                $post = Posts::where( 'id', $currentId-1 )->first();
                break;
                case 'shuffle':
                $post = Posts::inRandomOrder()->first();
                break;
                default:
                $post = Posts::inRandomOrder()->first();
                break;
            }
            $post = $post ? new PostResponseResource( $post->toArray() ) : null;
        } catch ( \Throwable $th ) {
            Log::channel( 'telegram' )->error( '', [ $th->getMessage() ] );
        }
        return response()->json( $post );
    }
}
