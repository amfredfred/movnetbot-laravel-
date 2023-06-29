<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use SergiX44\Nutgram\Nutgram;

class FrontController extends Controller
 {
    /**
    * Handle the telegram webhook request.
    */

    public function __invoke( Nutgram $bot ) {
        return [ $bot->run(), 'bot is running!!' ];
    }
}