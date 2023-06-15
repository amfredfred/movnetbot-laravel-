<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Handlers\Type\Command;

class DeleteFileCommand extends Command
{
    protected string $command = 'command';

    protected ?string $description = 'A lovely description.';

    public function handle(Nutgram $bot): void
    {
        $bot::SaveUser($bot);
        $bot->sendMessage('This is a command!');
    }
}
