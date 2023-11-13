<?php

namespace App\Actions\AI;

use App\Actions\AI\VectorStores\PineconeClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class Embed
{
    use AsAction;

    public $commandSignature = 'embed';

    public function handle()

    { {
            $pineconeClient = new PineconeClient();
            $pinecone = $pineconeClient->getPineconeClient();
            $pineconeIndex = config('pinecone.index');

            $content = Str::of(File::get(storage_path('app/podcast.html')))
                ->after('<strong>')
                ->split('</strong>')
                ->map(fn (string $bit) => strip_tags($bit))
                ->toArray();

            $embeddings = OpenAI::embeddings()->create([
                'model' => 'text-embedding-ada-002',
                'input' => $content,
            ])->embeddings;

            $pinecone->index($pineconeIndex)->vectors()->upsert(
                vectors: collect($embeddings)->map(fn ($embedding, $index) => [
                    'id' => (string) $index,
                    'values' => $embedding->embedding,
                    'metadata' => [
                        'text' => $content[$index]
                    ]
                ])->toArray(),
                namespace: 'Aaron'
            );
        }
    }
}
