<?php

namespace App\Telegram\Conversations;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class UploadFileConversation extends Conversation {
    protected ?string $step = 'step1';

    public $title;
    public $caption;
    public $description;
    public $type;
    public $thumbnail;
    public $file;
    public $steptwomessege = 'Okay 👌, please upload your ';
    public $bouncedfrom;
    public $steptext = 'This is last step';
    public $nextstep;

    public function step1( Nutgram $bot ) {
        $bot->sendMessage(
            text: 'Alright, what do you want to upload? 😌',
            reply_markup: InlineKeyboardMarkup::make()
            ->addRow( InlineKeyboardButton::make( 'Video', callback_data: 'video' ), /*InlineKeyboardButton::make( 'Audio', callback_data: 'audio' )*/ )
            ->addRow( InlineKeyboardButton::make( 'Document', callback_data: 'document' ), /*InlineKeyboardButton::make( 'Photo', callback_data: 'photo' )*/ ),
        );
        $this->next( 'step2' );
    }

    public function step2( Nutgram $bot ) {
        if ( !$bot->isCallbackQuery() ) {
            $this->step1( $bot );
            return;
        }
        $this->type = $bot->callbackQuery()->data;
        $bot->sendMessage( $this->steptwomessege.' '.$this->type,
        reply_markup: InlineKeyboardMarkup::make()->addRow( InlineKeyboardButton::make( 'Discard all', callback_data: 'discard' ) ) );
        $this->next( 'step3' );
    }

    public function step3( Nutgram $bot ) {
        $message = $bot->message();
        $msgt = $message->getType();

        if ( $bot->isCallbackQuery() ) {
            if ( $bot->callbackQuery()->data === 'discard' ) {
                $this->discard( $bot );
                return;
            }
        }

        if ( $message->getText() ) {
            $this->caption = $message->getText() ;
        }

        if ( $msgt->value !== $this->type ) {
            $bot->sendMessage( "You sent a {$msgt->value} and not a".$this->type, reply_to_message_id:$message->message_id );
            $this->steptwomessege = 'Please upload a';
            $this->step2( $bot );
            return;
        }

        switch ( $msgt->value ) {
            case 'video':
            $this->file = [
                'mime_type'=> $message->video->mime_type,
                'caption'=>$this->caption,
                'file_size'=> $message->video->file_size,
                'username'=>$message->from->username,
                'parent_path'=> $bot->getFile( $message->video->file_id )->url(),
                'file_id'=> $message->video->file_id,
                'thumbnail'=> $this->thumbnail ?? $bot->getFile( $message->video->thumbnail->file_id )->url(),
            ];
            break;
            case 'audio':
            $this->file = [
                'mime_type'=> $message->audio->mime_type,
                'caption'=>$this->caption,
                'file_size'=> $message->audio->file_size,
                'username'=>$message->from->username,
                'parent_path'=> $bot->getFile( $message->audio->file_id )->url(),
                'file_id'=> $message->audio->file_id,
                'thumbnail'=> $this->thumbnail ?? $bot->getFile( $message->audio->thumbnail->file_id )->url(),
            ];
            break;
            case 'photo':
            $this->file = [
                'mime_type'=> $message->photo[ 1 ],
                'caption'=>$this->caption,
                'file_size'=> $message->photo[ 1 ]->file_size,
                'username'=>$message->from->username,
                'parent_path'=> $bot->getFile( $message->photo[ 1 ]->file_id )->url(),
                'file_id'=> $message->photo[ 1 ]->file_id,
                'thumbnail'=> $this->thumbnail,
            ];
            break;
            case 'document':
            $this->file = [
                'mime_type'=> $message->document->mime_type,
                'caption'=>$this->caption,
                'file_size'=> $message->document->file_size,
                'username'=>$message->from->username,
                'parent_path'=> $bot->getFile( $message->document->file_id )->url(),
                'file_id'=> $message->document->file_id,
                'thumbnail'=> $this->thumbnail ?? $bot->getFile( $message->document->thumbnail->file_id )->url(),
            ];
            break;
            default:
            $this->file = null;
            $bot->sendMessage( 'Something went wrong start again!!' );
            // return;
            break;
        }

        $this->laststep( $bot );
        $this->next( 'step4' );
    }

    public function step4( Nutgram $bot ) {
        if ( !$bot->isCallbackQuery() ) {
            $bot->sendMessage( 'Please select an option', );
            $this->nextstep = 'step4';
            $this->laststep( $bot );
            return;
        }

        $selected = $bot->callbackQuery()->data;
        switch ( $selected ) {
            case 'save':
            $this->save( $bot );
            break;
            case 'changecaption':
            $bot->sendMessage( 'Okay, send me the caption' );
            $this->next( 'changecaption' );
            break;
            case 'preview':
            # code...
            break;
            case 'uploadhtumbnail':
            $bot->sendMessage( 'Okay, send me the thumb photo' );
            $this->next( 'uploadhtumbnail' );
            break;
            case 'discard':
            $this->discard( $bot );
            break;
            default:
            $this->steptext = 'Choose an option from the list';
            $this->laststep( $bot );
            break;
        }
    }

    // Save file

    public function save( Nutgram $bot ) {
        $link = $bot::SaveFile( $this->file );
        $bot->sendMessage( 'Your '.$this->type.' Has been populated!\n\nLink: '.$link[ 'link' ]. '\nID: ```'.$link[ 'post_id' ].'```', parse_mode:ParseMode::HTML );
        $this->end();
    }

    // Discard session

    public function discard( Nutgram $bot ) {
        $bot->sendMessage( 'Upload has been discarded!!!' );
        $this->end();
    }

    // Send for  preview

    public function preview( Nutgram $bot ) {

    }

    //Thumbnail uploaded

    public function uploadhtumbnail( Nutgram $bot ) {
        $message = $bot->message();
        if ( $message->getType()->value !== 'photo' ) {
            $bot->message( 'Sorry only photo is allowed as a thumb!!' );
            $this->bouncedfrom = 'uploadhtumbnail';
            $this->laststep( $bot );
            return;
        }
        $this->thumbnail = $bot->getFile( $message->photo[ count( $message->photo ) -1 ]->file_id )->url();
        $this->file[ 'thumbnail' ] = $this->thumbnail;
        $this->steptext = 'Thumbnail added';
        $this->laststep( $bot );
        $this->next( 'step4' );
    }

    public function changecaption( Nutgram $bot ) {
        $message = $bot->message();
        if ( !$message->getText() ) {
            $bot->sendMessage( 'Please send a text caption' );
            $this->bouncedfrom = 'changecaption';
            return;
        }
        $this->caption = $message->getText();
        $this->file[ 'caption' ] = $this->caption ;
        $this->steptext = 'Caption added';
        $this->laststep( $bot );
        $this->next( 'step4' );
    }

    // Last step

    public function laststep( Nutgram $bot ) {
        $bot->sendMessage(
            text: $this->steptext,
            reply_markup: InlineKeyboardMarkup::make()
            ->addRow( InlineKeyboardButton::make( 'Save', callback_data: 'save' ),
            InlineKeyboardButton::make( 'Discard', callback_data: 'discard' ),
            InlineKeyboardButton::make( 'Preview', callback_data: 'preview' ) )
            ->addRow( InlineKeyboardButton::make( 'Caption', callback_data: 'changecaption' ), InlineKeyboardButton::make( 'Thumbnail', callback_data:'uploadhtumbnail' ) ),
        );

        if ( $this->nextstep ) {
            $this->next( $this->nextstep );
            $this->nextstep  = null;
        }

    }
}
