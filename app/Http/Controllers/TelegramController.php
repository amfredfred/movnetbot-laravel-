<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

class TelegramController extends Controller
 {
    public function srm( Nutgram $bot ) {
        $bot = new Nutgram( config( 'nutgram.token' ) );
        $bot->setRunningMode( Webhook::class );
        $bot->run();
        return [ 'Running Mode Is '=> 'WebHook!!' ];
    }
}