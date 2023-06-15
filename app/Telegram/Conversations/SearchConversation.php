<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SearchConversation extends Conversation
{
    protected $sesionText = 'Cool, i got you covered! ';

    public function start(Nutgram $bot)   {
    $bot->sendMessage($this->sesionText, reply_markup:InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make("Web", url:$bot::WebAppUrl()))
        ->addRow(InlineKeyboardButton::make("Telegram", switch_inline_query_current_chat:"hot"))
     );
        $this->next('step1');
    }

    public function step1(Nutgram $bot)  {
    $bot->sendMessage('hey 👋, did you find what you were looking for? ', reply_markup:InlineKeyboardMarkup::make()
        ->addRow(
            InlineKeyboardButton::make("Request a movie", callback_data:'request'),
        InlineKeyboardButton::make("Try Again", callback_data:'retry')
    ),reply_to_message_id:$bot->message()->message_id);
     $this->next('step2');
    }

    public function step2(Nutgram $bot){
        if(!$bot->isCallbackQuery()){
            $this->end();
        }
        $option = $bot->callbackQuery()->data;
        if($option==='request'){
            $bot->sendMessage('Alright, tap the /request command');
            $bot->sendMessage('/request', reply_to_message_id:$bot->message()->message_id);
            $this->end();
        }
        else if($option === 'retry'){
            $this->sesionText = "Okay, lt try again";
            $this->start($bot);
        }
    }

}
