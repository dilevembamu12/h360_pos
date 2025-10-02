<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

use Illuminate\Database\Seeder;
use Modules\OpenAI\Entities\ContentType;

class LongArticleContentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContentType::upsert([
            ['name' => 'Long Article', 'slug' => 'long_article']
        ], ['slug']); 
    }
}
