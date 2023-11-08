<?php



use App\Actions\AI\Prompts\FirstPrompt;
use App\Actions\AI\Prompts\FirstPrompt4;
use App\Actions\AI\VectorStores\PineconeClient;
use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;
use \Probots\Pinecone\Client as Pinecone;

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


Route::get('/embeddings', function () {

    $pineconeClient = new PineconeClient();
    $pinecone = $pineconeClient->getPineconeClient();
    $index = config('pinecone.index');

    $values1 = [
        'My name is Aaron',
        // 'I live in San Diego',
        'tacos are delicious',
    ];

    $values2 = [
    ' My name is Luke',
    // 'I live in San Francisco',
    'tacos are delicious',
    ];

    $embeddings = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => $values1,
     ])->embeddings;


    $result = $pinecone->index($index)->vectors()->upsert(
    collect($embeddings)->map(fn ($embedding, $idx) => [
        'id' => (string) $idx,
        'values' => $embedding->embedding,
        'metadata' => [
            'text' => $values1[$idx]
        ]
    ])->toArray(),
    namespace: 'Aaron',);

        $embeddings = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $values2,
         ])->embeddings;


        $result = $pinecone->index($index)->vectors()->upsert(
        collect($embeddings)->map(fn ($embedding, $idx) => [
            'id' => (string) $idx,
            'values' => $embedding->embedding,
            'metadata' => [
                'text' => $values2[$idx]
            ]
        ])->toArray(),
        namespace: 'Luke',);

        // dd($result->json());

        // $pinecone->index($index)->vectors()->delete(deleteAll: true);
        // $pinecone->index($index)->vectors()->delete(['someId']);

    $question = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => [
            'Tell me something about Aaron.',
        ]
        ]);

    $result = $pinecone->index($index)->vectors()->query(vector: $question->embeddings[0]->embedding, namespace:'Luke', topK:4)->json();

    dd($result);

    //  $pinecone->index($index)->vectors()->upsert(
    //     vectors: [
    //     [
    //     'id' => 'testing',
    //     'values' => $embeddings,
    //     'meta' => [
    //         'name' => 'value'
    //     ]
    // ]
    // ]
    //     );

    dd($pinecone->index($index)->vectors()->fetch(


        [
        'testing'
    ])->json());




});




Route::get('/test', function(){
    dd(OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => 'Hello, my dog is cute',
     ]));
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

    $systemMessage = [
        'role' => 'system',
        'content' => 'You are a helpful assistant.',
    ];

   $result =  $prompt->handle(array_merge([$systemMessage],($messages)));

   $conversation->messages()->create([
        'content' => $result->choices[0]->message->content,
        'role' => 'assistant',
    ]);

    return redirect()->route('conversation', ['id' => $conversation->id]);
})->name('chat');
