<?php

namespace App\Actions\Prompts;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt3
{
    use AsAction;

    public $commandSignature = 'prompt3';


    public function run()
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Sup? *fist bump*'
                ]
            ]
        ])->choices[0]->message->content;
    }

    public function asCommand(Command $command)
    {
        $command->comment(
            $this->run()
        );
    }
}
