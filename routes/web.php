<?php

use App\Actions\Prompts\ChatPrompt;
use App\Actions\Prompts\FirstPrompt;
use Illuminate\Http\Request;
use App\Actions\Prompts\FirstPrompt1;
use App\Actions\Prompts\FirstPrompt4;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (FirstPrompt4 $firstPrompt4) {
    return response()->json($firstPrompt4->run('how many leafs are on a tree?'));
    // return $firstPrompt4->run('how many leafs are on a tree?');
    // return view('welcome');
});

Route::get('conversations/{id}', function ($id) {
    $conversation = $id == 'new' ? null : Conversation::find($id);
    return view('conversation', [
        'conversation' => $conversation
    ]);
})->name('conversation');

Route::post('chat/{id}', function (Request $request, FirstPrompt $prompt, $id) {
    if ($id == 'new') {
       $conversation = Conversation::create();
    } else {
        $conversation = Conversation::find($id);
    }

    $conversation->messages()->create([
        'content' => $request->input('prompt')
    ]);

    $messages = $conversation->messages->map(function(Message $message){
        return [
            'content' => $message->content,
            'role' => 'user',
        ];
    })->toArray();

   $result =  $prompt->handle($messages);

   $conversation->messages()->create([
        'content' => $result->choices[0]->message->content,
        'role' => 'assistant',
    ]);

    return redirect()->route('conversation', ['id' => $conversation->id]);
})->name('chat');
