<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResponseResource;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
 {

     public $qs;
     public $posts = [];

    public function map( $post ) {
        return  new PostResponseResource( $post );
    }

    public function search( Request $request ) {
        $posts = self::query( $request->json( 'q' ) );
        $posts = $posts? array_map( 'self::map',  $posts ) : $posts;
        return response()->json( $posts, 200 );
    }

    public function query( $query ) {
       try {
         $this->qs = collect(array_unique( explode( ' ', $query ) ));
         $this->posts  =  $query ? $this->qs->map( function($qs){
            return Posts::where( 'file_caption', 'LIKE', "%{$qs}%" )
            ->orWhere( 'file_type', 'LIKE', "%{$qs}%" )
            ->orWhere( 'file_uploader', 'LIKE', "%{$qs}%" )
            ->first();
        })->sort()->skip( 0 )->take( 10 )->toArray() 
        : Posts::inRandomOrder()->skip( 0 )->take( 10 )->get()->toArray();
       } catch (\Throwable $th) {
        Log::channel('telegram')->error("", [$th->getMessage()]);
       }
        return  array_unique($this->posts,SORT_REGULAR);
    }

    
}
