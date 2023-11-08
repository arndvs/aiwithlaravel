<?php

namespace App\Actions\AI\Prompts;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt2
{




    public function run(string $prompt)
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ])->choices[0]->message->content;
    }


}
