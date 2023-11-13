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

    //create pinecone client
    $pineconeClient = new PineconeClient();
    $pinecone = $pineconeClient->getPineconeClient();
    $index = config('pinecone.index');


    //values to be embedded
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


    // create embeddings for values1
    $embeddings = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => $values1,
    ])->embeddings;


    //upsert values1 vectors into pinecone
    $result = $pinecone->index($index)->vectors()->upsert(

        //map the embeddings to the values
        collect($embeddings)->map(fn ($embedding, $idx) => [
            'id' => (string) $idx,
            'values' => $embedding->embedding,
            'metadata' => [
                'text' => $values1[$idx]
            ]
        ])->toArray(),
        namespace: 'Aaron',
    );


    // create embeddings for values2
    $embeddings = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => $values2,
    ])->embeddings;

    // upsert values2 vectors into pinecone
    $result = $pinecone->index($index)->vectors()->upsert(

        collect($embeddings)->map(fn ($embedding, $idx) => [
            'id' => (string) $idx, // stringified index as id
            'values' => $embedding->embedding,  // embedding values
            'metadata' => [
                'text' => $values2[$idx]
            ]
        ])->toArray(),
        namespace: 'Luke',
    );


    // dd($result->json());

    // $pinecone->index($index)->vectors()->delete(deleteAll: true);
    // $pinecone->index($index)->vectors()->delete(['someId']);


    // embed the question
    $question = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => [
            'Tell me something about Aaron.',
        ]
    ]);

    // query pinecone for the most similar vectors
    $result = $pinecone->index($index)->vectors()->query(
        vector: $question->embeddings[0]->embedding, // question embedding
        namespace: 'Luke',
        topK: 4
    )->json();

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
        ]
    )->json());
});




Route::get('/test', function () {
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

    $messages = $conversation->messages->map(function (Message $message) {
        return [
            'content' => $message->content,
            'role' => 'user',
        ];
    })->toArray();





    //create pinecone client
    $pineconeClient = new PineconeClient();
    $pinecone = $pineconeClient->getPineconeClient();
    $index = config('pinecone.index');


    $question = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => $request->input('prompt')
    ]);


    // query pinecone for the most similar vectors
    $results = $pinecone->index($index)->vectors()->query(
        vector: $question->embeddings[0]->embedding,
        namespace: 'Aaron',
        topK: 4
    )->json();


    $systemMessage = [
        'role' => 'system',
        'content' => sprintf(
            'Base your answer on the February 2023 podcast episode between Tim Urban and Lex Fridman. Here are some snippets from that may help you answer: %s',
            collect($results['matches'])->pluck('metadata.text')->join("\n\n---\n\n"),
        ),
    ];


    $result =  $prompt->handle(array_merge([$systemMessage], ($messages)));

    $conversation->messages()->create([
        'content' => $result->choices[0]->message->content,
        'role' => 'assistant',
    ]);

    return redirect()->route('conversation', ['id' => $conversation->id]);
})->name('chat');
