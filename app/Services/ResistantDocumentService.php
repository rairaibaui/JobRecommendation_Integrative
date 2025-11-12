<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResistantDocumentService
{
    protected string $base;
    protected ?string $key;

    public function __construct()
    {
        $this->base = rtrim(config('services.resistant.base') ?? env('RESISTANT_API_BASE', 'https://api.resistant.ai/v2'), '/');
        $this->key = config('services.resistant.key') ?? env('RESISTANT_API_KEY');
    }

    /**
     * Upload and analyze a file using the Resistant.AI Documents API.
     * Returns parsed JSON response or null on failure.
     */
    public function analyzeFile(string $absolutePath): ?array
    {
        if (empty($this->key)) {
            Log::warning('ResistantDocumentService: API key not configured.');
            return null;
        }

        try {
            $response = Http::withToken($this->key)
                ->timeout(60)
                ->attach('file', file_get_contents($absolutePath), basename($absolutePath))
                ->post($this->base . '/documents:analyze', [
                    // adjust params per Resistant.AI docs as needed
                    'features' => json_encode(['classification', 'fields']),
                    'options' => json_encode(['return_raw_text' => true]),
                ]);

            if (! $response->ok()) {
                Log::warning('Resistant API error', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Resistant API request failed: ' . $e->getMessage());
            return null;
        }
    }
}
