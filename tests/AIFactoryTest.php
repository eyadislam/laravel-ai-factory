<?php

use FlorianDomgjoni\AIFactory\Concerns\HasAIFactory;
use FlorianDomgjoni\AIFactory\Tests\Database\Factories\DummyFactory;
use FlorianDomgjoni\AIFactory\Tests\Database\Models\Dummy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('throws an exception when ai fields are not defined', function () {
    try {
        $factory = new class extends Factory {
            use HasAIFactory;
            protected $model = Dummy::class;

            public function definition()
            {
                return [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                ];
            }
        };
        $factory->createWithAI();
    } catch (\Throwable $e) {
        $this->assertSame('You must define an aiFields() method in your factory.', $e->getMessage());
    }
});

it('throws an exception when ai fields are invalid', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'choices' => [[
                'message' => [
                    'content' => json_encode([
                        ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
                        ['name' => 'John Smith', 'email' => 'john@example.com'],
                    ]),
                ],
            ]],
        ], 200),
    ]);
    try {
        DummyFactory::new()->createWithAI(['name' => true]);
    } catch (\Throwable $e) {
        $this->assertSame("Invalid value for key 'name'. Must be a string (AI prompt) or callable (manual).", $e->getMessage());
    }
});

it('can create records with AI-generated fields', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'choices' => [[
                'message' => [
                    'content' => '```json' . json_encode([
                        ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
                        ['name' => 'John Smith', 'email' => 'john2@example.com'],
                    ]) . '```',
                ],
            ]],
        ], 200),
    ]);
    Dummy::factory()->count(2)->createWithAI();
    $this->assertSame(2, Dummy::count());
});

it('throws an exception when api response is invalid', function () {
    Exceptions::fake();
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'message' => 'Invalid response',
        ], 401),
    ]);
    try {
        Dummy::factory()->count(2)->createWithAI();
    } catch (\Throwable $e) {
        $this->assertSame('[AI Factory] AI response failed with message: {"message":"Invalid response"} and code 401', $e->getMessage());
    }
    $this->assertSame(0, Dummy::count());
});

it('throws an exception when model is not defined', function () {
    try {
        $factory = new class extends Factory {
            use HasAIFactory;

            public function definition()
            {
                return [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                ];
            }
        };
        $factory->createWithAI();
    } catch (\Throwable $e) {
        $this->assertSame('You must define a $model property in your factory.', $e->getMessage());
    }
});
