<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

class FrontController extends Controller
 {
    /**
    * Handle the telegram webhook request.
    */

    public function __invoke( Nutgram $bot ) { 
        $bot = new Nutgram( config( 'nutgram.token' ) );
        $hooked = $bot->setRunningMode( Webhook::class );
        $bot->run();
        return [ 'Running Front'=>[   $hooked ] ];
    }
}