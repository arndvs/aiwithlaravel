<?php

use App\Actions\Prompts\FirstPrompt1;
use App\Actions\Prompts\FirstPrompt2;
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


Artisan::command('prompt0', function () {
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

Artisan::command('prompt1', function () {
    $this->comment(
        FirstPrompt1::make()->run()
    );
})->purpose('make and run');

Artisan::command('prompt2', function (FirstPrompt2 $firstPrompt2) {
    $this->comment(
        $firstPrompt2->run()
    );
})->purpose('inject as dependency');

// call the action as a command with pa prompt3, pa prompt4
