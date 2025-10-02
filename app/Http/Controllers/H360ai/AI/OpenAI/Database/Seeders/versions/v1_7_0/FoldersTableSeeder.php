<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoldersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $drive = DB::table('folders')->where('slug', 'drive-' . $user->id)->exists();

            if(!$drive) {
                DB::table('folders')->insert(
                    [
                        'name' => 'Drive',
                        'slug' => 'drive-' . $user->id,
                        'user_id' => $user->id,
                    ]
                );
            } 
        }

    }
}
