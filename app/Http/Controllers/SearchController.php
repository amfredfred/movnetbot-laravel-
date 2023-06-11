<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResponseResource;
use App\Models\Posts;
use Illuminate\Http\Request;

class SearchController extends Controller
 {

    public function map( $post ) {
        return  new PostResponseResource( $post );
    }

    public function search( Request $request ) {
        $posts = self::query( $request->json( 'q' ) );
        $posts = $posts? array_map( 'self::map',  $posts ) : $posts;
        return response()->json( $posts, 200 );
    }

    public function query( $query ) {
        $contents = [];
        $explodQuery = array_unique( explode( ' ', $query ) );

        foreach ( $explodQuery as $key => $value ) {
            $posts = Posts::where( 'file_caption', 'LIKE', "%{$value}%" )
            ->orWhere( 'file_caption', 'LIKE', "%$query%" )
            ->orWhere( 'file_type', 'LIKE', "%{$value}%" )
            ->orWhere( 'file_type', 'LIKE', "%{$query}%" )
            ->orWhere( 'file_uploader', 'LIKE', "%{$value}%" )
            ->orWhere( 'file_uploader', 'LIKE', "%{$query}%" )
            ->orWhere( 'file_size', 'LIKE', "%{$value}%" )
            ->orWhere( 'file_size', 'LIKE', "%{$query}%" )
            ->skip( 0 )
            ->take( 9 )
            ->orderBy( 'updated_at', 'desc' )
            ->get();
            array_push( $contents, $posts->toArray() );
        }
        $contents = $contents[ 0 ] ;
        return $contents;
    }
}
