<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResponseResource;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
 {


    public function search( Request $request ) {
        $qResults = $this->query( $request->json( 'q' ) ) ;

        $posts = [];
        try {
            foreach ( $qResults as $key => $result ) {
                array_push( $posts, new PostResponseResource( $result ) );
            }
        } catch ( \Throwable $th ) {
            Log::channel( 'telegram' )->warning( 'SEARCH_QUERY_ERROR: ', [ $th->getMessage() ] );
            $posts = [];
        }

        return response()->json( $posts, 200 );
    }

    public $queryString;

    public function query( $query ) {
        $results = collect([]);
        try {
            $qs =  array_unique( explode( ' ', $query ) ) ;
            foreach ( $qs as $key => $string ) {
                $this->queryString = $string;
                $posts = Posts::where(function (Builder $query) {
                    $query
                     /*@devfred*/->where( 'file_caption', 'LIKE', "%{$this->queryString}%" )
                     /*@devfred*/->orWhere( 'file_type', 'LIKE', "%{$this->queryString}%" )
                     /*@devfred*/->orWhere( 'file_uploader', 'LIKE', "%{$this->queryString}%" );
                })->orderBy('updated_at', "desc")->take( 10 )->get();
                $results->push($posts);
            }
        } catch ( \Throwable $th ) {
            Log::channel( 'telegram' )->warning( 'QUERY_ERROR: ', [ $th->getMessage() ] );
        }
        return $results->collapse()->unique()->toArray() ;
    }
}
