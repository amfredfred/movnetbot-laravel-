<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Http\Controllers\InlineQueryController;
use SergiX44\Nutgram\Nutgram;
use App\Telegram\Commands\DeleteFileCommand;
use App\Telegram\Commands\UpdateFileCommand;
use App\Telegram\Conversations\AdvertConversation;
use App\Telegram\Conversations\DonateConversation;
use App\Telegram\Conversations\LatestConversation;
use App\Telegram\Conversations\ReportConversation;
use App\Telegram\Conversations\RequestConversation;
use App\Telegram\Conversations\SearchConversation;
use App\Telegram\Conversations\StartConversation;
use App\Telegram\Conversations\StatsConversation;
use App\Telegram\Conversations\SuggestConversation;
use App\Telegram\Conversations\UploadFileConversation;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

$bot->onInlineQuery(function (Nutgram $bot)   {
    $inlineController = new  InlineQueryController();
    return $inlineController->start($bot);
} );

//
$bot->onText( '/start@movnetbot',  StartConversation::class );
$bot->onText( '/search@movnetbot',  SearchConversation::class );
$bot->onText( '/request@movnetbot',  RequestConversation::class );
$bot->onText( '/random@movnetbot',  SuggestConversation::class );

//
$bot->onCommand( 'start', StartConversation::class );
$bot->onCommand( 'search', SearchConversation::class );
$bot->onCommand( 'random',  SuggestConversation::class );
$bot->onCommand( 'latest', LatestConversation::class );
$bot->onCommand( 'request', RequestConversation::class );
$bot->onCommand( 'stats', StatsConversation::class );
// $bot->onCommand( 'report', ReportConversation::class );
// $bot->onCommand( 'donate', DonateConversation::class );
// $bot->onCommand( 'advert', AdvertConversation::class );

//Admins Area
$bot->onCommand( 'update', UpdateFileCommand::class );
$bot->onCommand( 'delete', DeleteFileCommand::class );
$bot->onCommand( 'upload', UploadFileConversation::class );

// $bot->run();