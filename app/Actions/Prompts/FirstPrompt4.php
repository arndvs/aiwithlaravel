<?php

namespace App\Actions\Prompts;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt4
{
    use AsAction;

    public $commandSignature = 'prompt4 {prompt : the user prompt}';


    public function run(string $prompt)
    {
        dd(OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ]
            ]
                ]));
    }

    public function asCommand(Command $command)
    {
        $command->comment(
            $this->run($command->argument('prompt'))
        );
    }
}
