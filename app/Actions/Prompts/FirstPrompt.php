<?php

namespace App\Actions\Prompts;

use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt
{

    public function handle(array $messages)
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages
                ]);
    }

}
