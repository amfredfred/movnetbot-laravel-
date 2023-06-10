<?php

namespace App\Helpers;

use App\Models\BotUsers;
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

     static function MakeTitle( $url, $path ) {
        return "Untitiled Video";
    }

    static function SaveUser(Nutgram $bot){
        Log::channel('telegram')->alert('', [$bot->chat()]);
    }

}