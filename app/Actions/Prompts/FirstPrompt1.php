<?php

namespace App\Actions\Prompts;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt1
{
    use AsAction;

    public function run()
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'too legit to...'
                ]
            ]
        ])->choices[0]->message->content;
    }


}
