<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostsResource;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class InlineQueryController extends Controller
{

    public  $bot;
    public $postsarray = [];

    public function start(Nutgram $bot){
    $this->bot = $bot;
    $inlineQuery = $bot->inlineQuery();
    $query = $inlineQuery->query;
        $search = new SearchController();
        $posts = collect( $search->query($query));
         try {

          $posts->each(function ($post){
                $bot = $this->bot;
                $post = new PostsResource($post);
                    $result =   [
                        'id'=> $bot::RandomString(4),
                        'type'=>'article',
                        'title'=> $bot::MakeTitle($post['file_caption'],''),
                        'description'=>$post['file_caption'],
                        'thumbnail_url'=> $bot::ThumbUrl($post['file_thumbnails']),
                        'input_message_content'=>[
                            'message_text'=> $post['file_caption']."\n\n".$bot::WatchUrl($post['file_id'])."\n\n",
                            'parse_mode'=>ParseMode::HTML,
                        ],
                        'reply_markup' => InlineKeyboardButton::make()
                        ->addRow( InlineKeyboardButton::make( 'Watch | Download HD', url:$bot::WatchUrl( $post[ 'file_id' ] ) ) )
                    ];

                    array_push($this->postsarray, $result);
            });

        $bot->answerInlineQuery($this->postsarray);

        } catch (\Throwable $th) {
            // Log::channel('telegram')->warning("INLINE_QUERY: ", [$th]);
        }

        $bot::SaveUser($bot);
    }
}
