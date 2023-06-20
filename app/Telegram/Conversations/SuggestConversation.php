<?php

namespace App\Telegram\Conversations;

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

class SuggestConversation extends Conversation {

    public $sesionText = 'Okay, Wait';

    public function start( Nutgram $bot ) {

        try {
            $message = $bot->message();
            $chat = $bot->chat();
            $bot->sendMessage(
                $this->sesionText,
                reply_to_message_id:$message->message_id,
                chat_id:$chat->id,
                reply_markup:InlineKeyboardMarkup::make()
                ->addRow( InlineKeyboardButton::make( 'Cancel', callback_data:'cancel' ) )
            );
            $search = new SearchController();
            $post = collect( $search->query( '' ) )->first();
            $bot::SaveUser( $bot );
            if ( $post ) {
                $thumb = public_path( $post[ 'file_thumbnails' ] );
                $exists = file_exists( $thumb );
                if ( !$exists ) {
                    $bot->sendMessage(
                        parse_mode:ParseMode::HTML,
                        text: "<b> {$post[ 'file_caption' ]} </b>",
                        reply_markup:InlineKeyboardMarkup::make()
                        ->addRow( InlineKeyboardButton::make( 'Search for movie', switch_inline_query_current_chat:'new alert' ) )
                        ->addRow( InlineKeyboardButton::make( 'Watch | Download HD', web_app:new WebAppInfo( url:$bot::WatchUrl( $post[ 'file_id' ] ) ) ) )
                        ->addRow( InlineKeyboardButton::make( 'Retry', callback_data:'another' ) )
                    );
                    return;
                }
                $photo = fopen( $thumb, 'r+' );
                $bot->sendPhoto(
                    InputFile::make( $photo ),
                    parse_mode:ParseMode::HTML,
                    caption: "<b> {$post[ 'file_caption' ]} </b>",
                    reply_markup:InlineKeyboardMarkup::make()
                    ->addRow( InlineKeyboardButton::make( 'Search for movie', switch_inline_query_current_chat:'new alert' ) )
                    ->addRow( InlineKeyboardButton::make( 'Watch | Download HD', web_app:new WebAppInfo( url:$bot::WatchUrl( $post[ 'file_id' ] ) ) ) )
                    ->addRow( InlineKeyboardButton::make( 'Retry', callback_data:'another' ) )
                );
            }
        } catch ( \Throwable $th ) {
            Log::channel( 'telegram' )->error( '', [ $th->getMessage() ] );
        }

        $this->next( 'step2' );
    }

    public function step2( Nutgram $bot ) {
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

