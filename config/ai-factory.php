<?php

return [
    'driver' => env('FACTORY_AI_DRIVER', 'openai'),

    'defaults' => [
        // Optional per-model driver override
        // App\Models\User::class => 'gemini',
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => 'gpt-4o-mini',
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => 'gemini-pro',
    ],

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'model' => 'deepseek-chat',
    ],
];
