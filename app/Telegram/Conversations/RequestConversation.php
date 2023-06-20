<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Illuminate\Support\Str;

class RequestConversation extends Conversation
 {

    public $requesting = 'Okay, what are you requesting?';
    public $requested = '';
    public $requestedtitle;

    public function start( Nutgram $bot ) {
        $message = $bot->message();
        $chat = $message->chat;
        $bot::SaveUser( $bot );
        if ( !$chat->isPrivate() ) {
            $bot->sendMessage(
                text: "Hi {$chat->username}, for admins to see your request, send it to my inbox... 🙏",
                reply_to_message_id:$message->message_id,
                reply_markup: InlineKeyboardMarkup::make()
                ->addRow( InlineKeyboardButton::make( 'Open Chat', url: $bot::BotLauncher() ) )
            );
            $this->end();
            return;
        }
        $bot->sendMessage(
            $this->requesting,
            reply_markup:InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make( 'Movies', callback_data:'movies' ),
                InlineKeyboardButton::make( 'Cancel', callback_data:'cancel' )
            )
        );
        $this->next( 'step2' );
    }

    public function step2( Nutgram $bot ) {
        if ( !$bot->isCallbackQuery() ) {
            $this->requesting = 'Sorry, please chooe a respective option';
            $this->start( $bot );
            return;
        }

        switch ( $bot->callbackQuery()->data ) {
            case 'movies':
            $this->requested = 'movie';
            $bot->sendMessage(
                'Alright,  send me the movie title or descibe it!!'
            );
            break;
            case 'cancel':
            $bot->sendMessage( 'Alright, session cancelled!!!' );
            $this->end();
            default:
            # code...
            break;
        }

        $this->next( 'laststep' );
    }

    public function laststep( Nutgram $bot ) {
        $this->requestedtitle = $bot->message()->getText();
        $bot->sendMessage( "You requested for {$this->requested} {$this->requestedtitle} ." );
        $bot->sendMessage( 'Admins has been notified. 😍😍' );
        $bot->sendMessage(
            'Also, try the search movies with the /search command...',
            reply_markup:InlineKeyboardMarkup::make()
            ->addRow( InlineKeyboardButton::make( 'Search now', switch_inline_query_current_chat:"{$this->requestedtitle}" ) )
        );
        $bot->sendMessage(
            "===============\nNEW {$this->requested} REQUEST\nTitle: {$this->requestedtitle}\n===============", chat_id: '@movnetrequest'
        );
        $this->end();
    }

}
