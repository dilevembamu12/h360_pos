<?php

return [
    'name' => 'OpenAI',

    //Omit Languages for content generate
    'language' => [
        'Bengali' => 'Bengali',
        'Chinese' => 'Chinese',
    ],

    'speech_language' => [
        'Byelorussian' => 'Byelorussian',
        'Bengali' => 'Bengali',
        'Chinese' => 'Chinese',
    ],

    'text_to_speech_language' => [
        'Byelorussian' => 'Byelorussian',
        'Estonian' => 'Estonian'
    ],

    'fixedTemperatureModels' => [
        'o1-mini',
        'o1-preview',
        'o1',
        'o3-mini'
    ],

    "codeLevel" => [
        "Beginner",
    ],

    "codeLanguage" => [
        "PHP",
    ],

    'roleBasedModels' => [
        'gpt-4o-mini' => 'user',
        'gpt-4o' => 'user',
        'gpt-4' => 'user',
        'gpt-3.5-turbo' => 'user',
        'o1-preview' => 'user',
        'o1-mini' => 'user',
        'o1-preview' => 'user',
        'o1' => 'developer',
        'o3-mini' => 'developer'
    ],

    // For watch demo 
    'demo_url' => 'https://www.youtube.com/watch?v=qTgPSKKjfVg',

    'image_to_video_url' => 'https://api.stability.ai/v2beta/image-to-video',
    'fetch_video_url' => 'https://api.stability.ai/v2beta/image-to-video/result/'
];
