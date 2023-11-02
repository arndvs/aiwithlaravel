<?php

namespace App\Actions\Prompts;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class ChatPrompt
{
    use AsAction;

    public $commandSignature = 'prompt {prompt : The user prompt}';

    public function handle(string $prompt)
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $prompt,
        ]);
    }

    public function asCommand(Command $command)
    {
        $command->comment($this->handle($command->argument('prompt')));
    }
}
