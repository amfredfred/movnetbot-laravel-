<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Http\Controllers\SearchController;
use App\Http\Resources\PostsResource;
use SergiX44\Nutgram\Nutgram;
use App\Telegram\Commands\DeleteFileCommand;
use App\Telegram\Commands\FileRequestCommand;
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


$bot->onInlineQuery(function(Nutgram $bot){
    $findings=[];
    $inlineQuery = $bot->inlineQuery();
    $query = $inlineQuery->query;
        $search = new SearchController($query);
        $posts = $search->query($query);
        if(count($posts) && $posts[0] ){
            foreach ($posts as $postKey => $post) {
                    $post = new PostsResource($post);
                    $newResult = [
                        'id'=> $bot::RandomString(4),
                        'type'=>'article',
                        'title'=> $bot::MakeTitle($post['file_caption'],''),
                        'description'=>$post['file_caption'],
                        'thumbnail_url'=> $bot::ThumbUrl($post['file_thumbnails']),
                        'input_message_content'=>[
                            'message_text'=> $post['file_caption']."\n\n".$bot::WatchUrl($post['file_id'])."\n\n",
                            'parse_mode'=>ParseMode::HTML
                        ],
                        'reply_markup' => InlineKeyboardMarkup::make()
                        ->addRow(InlineKeyboardButton::make('Watch | Download Full HD', url:$bot::WebAppUrl($post['file_id']))),
                    ];
                    array_push($findings, $newResult);
                }
        }

    $bot::SaveUser($bot);
    $bot->answerInlineQuery($findings);
});


$bot->onMessage(function (Nutgram $bot){
    $message = $bot->message();
    $chat = $bot->chat();
    $bot->sendMessage('Hey');
    Log::channel('telegram')->alert('HEY', [ $message]);
});

//
$bot->onText('/start@movnetbot',  StartConversation::class);
$bot->onText('/search@movnetbot',  SearchConversation::class);

$bot->onCommand('start', StartConversation::class);
$bot->onCommand('search', SearchConversation::class);
$bot->onCommand('suggest',  SuggestConversation::class);
$bot->onCommand('latest', LatestConversation::class);
$bot->onCommand('request', RequestConversation::class);
$bot->onCommand('report', ReportConversation::class);
$bot->onCommand('donate', DonateConversation::class);
$bot->onCommand('advert', AdvertConversation::class);
$bot->onCommand('stats', StatsConversation::class);

//Admins Area
$bot->onCommand('update', UpdateFileCommand::class);
$bot->onCommand('delete', DeleteFileCommand::class);
$bot->onCommand('request', FileRequestCommand::class);
$bot->onCommand('upload', UploadFileConversation::class);