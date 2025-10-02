<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_2_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderManagerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codeLanguageData = [
            [
                "type" => "dropdown-with-image",
                "label" => "Code Language",
                "name" => "code_language",
                "value" => [
                    ["label" => "PHP", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\php.png"],
                    ["label" => "Java", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\java.png"],
                    ["label" => "Rubby", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\rubby.jpg"],
                    ["label" => "Python", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\python.png"],
                    ["label" => "C#", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\c-sharp.png"],
                    ["label" => "Go", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\go-lang.png"],
                    ["label" => "Kotlin", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\kotlin.png"],
                    ["label" => "HTML", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\html-5.png"],
                    ["label" => "Javascript", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\js.png"],
                    ["label" => "TypeScript", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\typescript.png"],
                    ["label" => "SQL", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\mysql.png"],
                    ["label" => "NoSQL", "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\no-sql.png"]
                ],
                "visibility" => true
            ]
        ];

        $providers = \DB::table('preferences')->where('category', 'code')->get();

        foreach ($providers as $provider) {
            $decodedValue = json_decode($provider->value, true);

            $hasDropdown = array_filter($decodedValue, fn($item) => isset($item['type']) && $item['type'] === 'dropdown-with-image');

            if (empty($hasDropdown)) {
                $codeValue = array_merge($decodedValue, $codeLanguageData);

                \DB::table('preferences')
                    ->where('field', $provider->field)
                    ->update(['value' => json_encode($codeValue)]);
            }
        }

        $artStyleData = [
            [
                "type" => "dropdown-with-image",
                "label" => "Image Art Style",
                "name" => "image_art_style",
                "value" => [
                    [
                        "label" => "Normal",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\normal.jpg",
                    ],
                    [
                        "label" => "3D Model",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\3d-animation.png",
                    ],
                    [
                        "label" => "Analog Film",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\analog-film.jpg",
                    ],
                    [
                        "label" => "Anime",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\anime.png",
                    ],
                    [
                        "label" => "Cinematic",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\cinematic.jpg",
                    ],
                    [
                        "label" => "Comic Book",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\comic.png", 
                    ],
                    [
                        "label" => "Digital Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\digital-art.png",
                    ],  
                    [
                        "label" => "Enhance",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\enhance.png",
                    ],
                    [
                        "label" => "Fantasy Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\fantasy.png",
                    ],
                    [
                        "label" => "Isometric",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\isometric.jpg",
                    ],
                    [
                        "label" => "Line Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\line-art.png",
                    ],
                    [
                        "label" => "Low Poly",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\low-poly.png",
                    ],
                    [
                        "label" => "Modeling Compound",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\modeling-compound.png",
                    ],
                    [
                        "label" => "Neon Punk",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\neon-punk.png",
                    ],
                    [
                        "label" => "Origami",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\origami.jpg",
                    ],
                    [
                        "label" => "Photographic",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\photographic.jpg",
                    ],
                    [
                        "label" => "Pixel Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\pixel-art.jpg",
                    ],
                    [
                        "label" => "Tile Texture",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\tile-texture.jpg",  
                    ],  
                    [
                        "label" => "Water Color",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\water-color.png",
                    ]
                ],
                "visibility" => true,
            ]
        ];
        
        $imageProviders = \DB::table('preferences')->where('category', 'imagemaker')->get();

        foreach ($imageProviders as $imageProvider) {
            $decodedValue = json_decode($imageProvider->value, true);

            $hasDropdownImage = array_filter($decodedValue, fn($item) => isset($item['type']) && $item['type'] === 'dropdown-with-image');

            if (empty($hasDropdownImage)) {
                $imageValue = array_merge($decodedValue, $artStyleData);

                \DB::table('preferences')
                    ->where('field', $imageProvider->field)
                    ->update(['value' => json_encode($imageValue)]);
            }
        }

    }
}   
