<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

class TelegramController extends Controller
 {
    public function handle( Nutgram $bot ) {
        $bot = new Nutgram( config( 'nutgram.token' ) );
        $hooked = $bot->setRunningMode( Webhook::class );
        $bot->run();
        return [ 'Running Front'=>[ $hooked ] ];
    }
}