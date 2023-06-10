<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Http\Resources\PostsResource;
use SergiX44\Nutgram\Nutgram;
use Nutgram\Laravel\Facades\Telegram;
use \Illuminate\Support\Str;
use SergiX44\Nutgram\Telegram\Types\Inline\InlineQueryResult;
use App\Models\Posts;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Inline\InlineQueryResultArticle;
use SergiX44\Nutgram\Telegram\Types\Inline\InlineQueryResultVideo;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

use function PHPSTORM_META\type;

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

    $saved = Posts::create([
        "file_id"=>$post_id,
        "file_type"=> $video->mime_type ,
        "file_caption"=> $caption,
        "file_size"=> $bot::KbTobB($video->file_size),
        "file_uploader"=> $chat->username,
        "file_views"=>0,
        "file_downloads"=>0,
        "file_parent_path"=>"",
        "file_description"=>"",
        "file_remote_id"=> $video->file_id,
        "file_thumbnails"=>  'uploads/thumbnails/'.$post_id.'.jpg',
        "file_download_link"=> $linkToPost,
    ]);
    Log::channel('telegram')->info('', [$linkToPost, $message->from->username ]);
});

$bot->onInlineQuery(function(Nutgram $bot){
    $findings=[];
    $inlineQuery = $bot->inlineQuery();
    $query = $inlineQuery->query;
    $explodQuery = array_unique(explode(' ', $query));
    foreach ($explodQuery as $key => $value) {
        $posts = Posts::where('file_caption', 'LIKE', "%{$value}%")
                ->orWhere('file_caption', 'LIKE', "%$query%")
                ->orWhere('file_type', 'LIKE', "%{$value}%")
                ->orWhere('file_type', 'LIKE', "%{$query}%")
                ->skip(0)
                ->take(2)
                ->orderBy('updated_at', 'desc')
                ->get();
        foreach ($posts->toArray() as $postKey => $post) {
            if(count($post)){
                $post = new PostsResource($post);
                $newResult = [
                    'id'=> $bot::RandomString(4),
                    'type'=>'article',
                    // 'video_file_id'=> $post['file_remote_id'],
                    'title'=> $bot::MakeTitle('',''),
                    'description'=>$post['file_caption'],
                    'thumbnail_url'=>config('app.url').$post['file_thumbnails'],
                    'input_message_content'=>[
                        'message_text'=> $post['file_caption'].' https://www.youtube.com/watch?v=Vv4OfhY23eg',
                        'parse_mode'=>ParseMode::HTML
                    ],
                      'reply_markup' => InlineKeyboardMarkup::make()
                       ->addRow(InlineKeyboardButton::make('Watch | Download Full HD', url:'https://t.me/denofmovies_bot/movies?startapp=command')),
                ];
                array_push($findings, $newResult);
            }
        }
    }
    // $pots = new PostsResource($pots);
    $bot->answerInlineQuery($findings);
    $bot::SaveUser($bot);
    Log::channel('telegram')->info('', [$bot->inlineQuery()->query,   $explodQuery ]);
});