<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Http\Controllers\SearchController;
use App\Http\Resources\PostsResource;
use SergiX44\Nutgram\Nutgram;
use \Illuminate\Support\Str;
use App\Models\Posts;
use App\Telegram\Commands\DeleteFileCommand;
use App\Telegram\Commands\FileRequestCommand;
use App\Telegram\Commands\StartBotCommand;
use App\Telegram\Commands\UpdateFileCommand;
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

$bot->onVideo(function (Nutgram $bot){
    $post_id = Str::random(5);
    $message = $bot->message();
    $chat = $bot->chat();
    $video = $message->video;
    $caption = ($message->caption ?? "No Caption");
    $vur = $bot->getFile($video->file_id)->url();
    $tur = $bot->getFile($video->thumbnail->file_id)->url();
    try {
        $bot::DownloadMedia($tur, public_path('uploads/thumbs/'.$post_id.'.'.pathinfo($tur, PATHINFO_EXTENSION)));
        // $bot::DownloadMedia($vur, public_path('uploads/videos/'.$post_id.'.'.pathinfo($vur, PATHINFO_EXTENSION)));
    } catch (\Throwable $th) {
        Log::channel('telegram')->info('', [$th->getMessage()]);
    }

    $linkToPost = config('app.view_wesite').$post_id;
    $bot::SaveUser($bot);
        $saved = Posts::create([
            "file_id"=>$post_id,
            "file_type"=> $video->mime_type ,
            "file_caption"=> $caption,
            "file_size"=> $bot::KbTobB($video->file_size),
            "file_uploader"=> $chat->username,
            "file_views"=>0,
            "file_downloads"=>0,
            "file_parent_path"=> $vur,
            "file_description"=>"",
            "file_remote_id"=> $video->file_id,
            "file_thumbnails"=>  'uploads/thumbs/'.$post_id.'.jpg',
            "file_download_link"=> $linkToPost,
        ]);
    Log::channel('telegram')->info('', [$linkToPost, $message->from->username ]);
});

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
                        'title'=> $bot::MakeTitle('',''),
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


$bot->onCommand('start', StartBotCommand::class);
$bot->onCommand('update', UpdateFileCommand::class);
$bot->onCommand('delete', DeleteFileCommand::class);
$bot->onCommand('request', FileRequestCommand::class);