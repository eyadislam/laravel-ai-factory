<?php

return [
    'driver' => env('AI_FACTORY_DRIVER', 'openai'),

    'defaults' => [
        // Optional per-model driver override
        // App\Models\User::class => 'gemini',
    ],

    'openai' => [
        'api_key' => env('AI_FACTORY_OPENAI_API_KEY'),
        'model' => env('AI_FACTORY_OPENAI_MODEL', 'gpt-4o-mini'),
    ]
];
