<?php

namespace App\Telegram\Conversations;

use App\Models\BotUsers;
use App\Models\Posts;
use DateTime;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class StatsConversation extends Conversation
 {
    public function start( Nutgram $bot )
 {
        $users = BotUsers::all();
        // $posts = Posts::all();
        $template = "👥 Total Users: {$users->count()}";
        $template .= "\n\n⏳🕴️ Users Today: {$users->where('updated_at', '>=', new DateTime('yesterday'))->count()}";
        $template .= "\n\n🆕🤱 Users Today: {$users->where('created_at', '>=', new DateTime('yesterday'))->count()}";
        $template .= "\n\n😲 Total Requests: {$users->sum('query_count')}";
        $template  .= "\n\n🕗 more coming soon...";
        $template = ( string ) $template;
        $bot->sendMessage( text: $template );
        $this->end();
    }

    //     public function secondStep( Nutgram $bot )
    // {
    //         $bot->sendMessage( 'Bye!' );
    //         $this->end();
    //     }
}