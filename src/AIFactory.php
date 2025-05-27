<?php

namespace FlorianDomgjoni\AIFactory;

// use YourVendor\FactoryAI\Drivers\OpenAIProvider;

use FlorianDomgjoni\AIFactory\Contracts\AIProviderInterface;
use FlorianDomgjoni\AIFactory\Drivers\OpenAIProvider;

class AIFactory
{
    protected array $drivers;

    protected string $defaultDriver;

    public function __construct()
    {
        $this->defaultDriver = config('factory-ai.driver', 'openai');

        $this->drivers = [
            'openai' => new OpenAIProvider,
        ];
    }

    public function driver(?string $name = null): AIProviderInterface
    {
        $name ??= $this->defaultDriver;

        if (! isset($this->drivers[$name])) {
            throw new \InvalidArgumentException("AI driver [$name] not supported.");
        }

        return $this->drivers[$name];
    }

    public function generateBulk(array $fields, int $count = 1): array
    {
        return $this->driver()->generateBulk($fields, $count);
    }
}
