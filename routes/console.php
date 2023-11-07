<?php

use App\Actions\AI\Prompts\TestPrompt;
use App\Actions\Prompts\FirstPrompt1;
use App\Actions\Prompts\FirstPrompt2;
use App\Actions\Prompts\FirstPrompt4;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use OpenAI\Laravel\Facades\OpenAI;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/


Artisan::command('prompt', function () {
    $this->comment(
        OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'roses are red'
                ]
            ]
        ])->choices[0]->message->content
            );
})->purpose('make and run');



// call the action as a command with pa prompt3, pa prompt4

Artisan::command('ai:test-prompt {prompt}', function (string $prompt) {
    $testPrompt = new TestPrompt();
    $response = $testPrompt->handle($prompt);

    $messages = $response['choices'][0]['message']['content'];

    $this->info($messages);
})->purpose('Ask a question in the context of a customer');
