<?php

namespace App\Actions\AI\Prompts;


use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt4
{

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


}
