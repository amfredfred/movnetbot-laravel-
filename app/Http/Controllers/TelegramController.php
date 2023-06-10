<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\RunningMode\Webhook;

class TelegramController extends Controller
 {
    public function handle( Nutgram $bot )
 {
        $bot->setRunningMode( Webhook::class );
        $bot->run() ;
        return [ 'running'=>true ];
    }
}
 