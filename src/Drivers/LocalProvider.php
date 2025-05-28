<?php

namespace FlorianDomgjoni\AIFactory\Drivers;

use FlorianDomgjoni\AIFactory\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocalProvider implements AIProviderInterface
{
    public function generateBulk(array $fields, int $count): array
    {
        $prompt = $this->buildPrompt($fields, $count);

        $response = Http::timeout(300)->withToken(config('ai-factory.local.api_key'))
            ->post(config('ai-factory.local.url'), [
                'model' => config('ai-factory.local.model'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        $content = $response->json('choices.0.message.content');
        Log::info('AI response received', ['response' => $content]);

        // Clean up formatting
        $cleaned = preg_replace('/^```(?:json)?|```$/m', '', trim($content));

        return json_decode($cleaned, true) ?? throw new \RuntimeException('Invalid AI response: JSON decoding failed');
    }

    protected function buildPrompt(array $fields, int $count): string
    {
        $fieldList = collect($fields)
            ->map(fn ($desc, $field) => "- `$field`: $desc")
            ->implode("\n");

        return <<<PROMPT
Generate {$count} fake data records in JSON array format. Each should contain:
$fieldList

Return only the JSON array.
PROMPT;
    }
}
