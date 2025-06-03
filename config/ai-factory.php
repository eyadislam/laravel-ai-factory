<?php

return [
    'driver' => env('AI_FACTORY_DRIVER', 'openai'),

    'openai' => [
        'api_key' => env('AI_FACTORY_OPENAI_API_KEY'),
        'model' => env('AI_FACTORY_OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'local' => [
        'url' => env('AI_FACTORY_LOCAL_URL', 'http://localhost:8080/v1/chat/completions'),
        'api_key' => env('AI_FACTORY_LOCAL_API_KEY', ''),
        'model' => env('AI_FACTORY_LOCAL_MODEL', 'Default'),
    ],
];
