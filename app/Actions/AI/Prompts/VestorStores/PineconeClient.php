<?php

namespace App\Actions\AI\VectorStores;

use Probots\Pinecone\Client as Pinecone;

class PineconeClient
{
    private Pinecone $client;

    public function __construct()
    {
        // Retrieving keys from config
        $apiKey = config('pinecone.api_key');
        $environment = config('pinecone.environment');
        $index = config('pinecone.index');

        $this->client = new Pinecone($apiKey, $environment);
    }

    public function getPineconeClient(): Pinecone
    {
        return $this->client;
    }
}
