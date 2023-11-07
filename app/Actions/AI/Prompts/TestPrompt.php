<?php

namespace App\Actions\AI\Prompts;

use OpenAI\Laravel\Facades\OpenAI;

class TestPrompt
{
    public function handle(string $prompt)
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system',
                    'content' => $prompt],

            ],
        ]);
    }
}
