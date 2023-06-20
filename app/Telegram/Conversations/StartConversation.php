<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class StartConversation extends Conversation
 {

    protected $commands = "/start - start the movies bot 💫🌟
/search - search for movies 🔍🔎
/random - get a random movie 〽️🪴
/request - request for a movie 📩📬
";

    //lastest - 5 movies last five days
    // /report - report a movie
    // /donate - keeps the servers up and running
    // /advert - promote your business here
    // /stats - get statistics

    public function start( Nutgram $bot ) {
        $bot::SaveUser( $bot );
        $message = $bot->message();
        $bot->sendMessage( "Welcome {$message->from->username}", reply_to_message_id:$message->message_id );
        $bot->sendMessage( "Available Commands\n\n{$this->commands}" );
        $this->next( 'step2' );
    }

    public function step2( Nutgram $bot ) {
        $bot->sendMessage( 'Nice ❤️❤️!' );
        $this->end();
    }
}
