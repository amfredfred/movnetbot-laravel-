<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\Posts;
use DateTime;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SuggestConversation extends Conversation
 {

    public $sesionText = 'Okay, Wait';

    public function start( Nutgram $bot )
 {
        $message = $bot->message();
        $chat = $bot->chat();
        $bot->sendMessage( $this->sesionText,
        reply_to_message_id:$message->message_thread_id,
        reply_markup:InlineKeyboardMarkup::make()
        ->addRow( InlineKeyboardButton::make( 'Cancel', callback_data:'cancel' ) ) );
        $posts = Posts::inRandomOrder()
        // ->skip( 0 )
        // ->take( 2 )
        // ->orWhere( 'file_downloads', '>=', 600 )
        // ->orWhere( 'updated_at', '<=',  new DateTime( 'now' ) )
        ->first();
        if ( count( $posts?->toArray() ) ) {
            $thumb = public_path( $posts[ 'file_thumbnails' ] );
            $exists = file_exists( $thumb );
            if ( !$exists )  return;
            $photo = fopen( $thumb, 'r+' );
            $bot->sendPhoto(
                InputFile::make( $photo ),
                caption:$posts[ 'file_caption' ],
                reply_markup:InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make( 'Website', url:$bot::WebAppUrl() ),
                    InlineKeyboardButton::make( 'Retry', callback_data:'another' )
                )->addRow( InlineKeyboardButton::make( 'Search for movie', switch_inline_query_current_chat:'new alert 2023' ) ) );
            }
            $this->next( 'secondStep' );
        }

        public function secondStep( Nutgram $bot )
 {
            if ( $bot->isCallbackQuery() ) {
                if ( $bot->callbackQuery()->data === 'cancel' ) {
                    $bot->sendMessage( 'Okay, session canceled!!!', reply_to_message_id:$bot->message()->message_id );
                    $this->end();
                    return;
                } else if ( $bot->callbackQuery()->data === 'another' ) {
                    $this->sesionText = "🙏😢 Okay {$bot->message()->from->first_name}, wait a sec";
                    $this->start( $bot );
                    return;
                }
            }
            $bot->sendMessage( 'Bye!' );
            $this->end();
        }

        public function retryStep() {

        }
    }
