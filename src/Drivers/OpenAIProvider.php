<?php

namespace FlorianDomgjoni\AIFactory\Drivers;

use FlorianDomgjoni\AIFactory\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProviderInterface
{
    public function generateBulk(array $fields, int $count): array
    {
        $prompt = $this->buildPrompt($fields, $count);

        $response = Http::withToken(config('ai-factory.openai.api_key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('ai-factory.openai.model'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (!$response->ok()) {
            throw new \RuntimeException('[AI Factory] AI response failed with message: ' . $response->body() . ' and code ' . $response->status());
        }

        $content = $response->json('choices.0.message.content');

        // Clean up formatting
        $cleaned = preg_replace('/^```(?:json)?|```$/m', '', trim($content));

        return json_decode($cleaned, true) ?? throw new \RuntimeException('[AI Factory] Invalid AI response: JSON decoding failed');
    }

    protected function buildPrompt(array $fields, int $count): string
    {
        $fieldList = collect($fields)
            ->map(fn($desc, $field) => "- `$field`: $desc")
            ->implode("\n");

        return <<<PROMPT
Generate {$count} fake data records in JSON array format. Each should contain:
$fieldList

Return only the JSON array.
PROMPT;
    }
}
