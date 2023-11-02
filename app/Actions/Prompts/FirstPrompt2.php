<?php

namespace App\Actions\Prompts;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt2
{
    use AsAction;



    public function run()
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Where are the tacos?'
                ]
            ]
        ])->choices[0]->message->content;
    }


}
