<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WordPressToLaravelSeeder extends Seeder
{
    public function run()
    {
        $batchSize = 100; // Process in batches of 100
        $offset = 0;
        $hasMore = true;

        dd("command run");

        // while ($hasMore) {
        //     /* Fetch posts from WordPress DB */
        //     $posts = DB::connection('wordpress')
        //         ->table('wp_posts')
        //         ->where('post_type', 'post') /* Only get blog posts */
        //         ->skip($offset)
        //         ->take($batchSize)
        //         ->get();

        //     if ($posts->isEmpty()) {
        //         $hasMore = false;
        //         break;
        //     }

        //     /* Prepare data for Laravel */
        //     $dataToInsert = [];
        //     foreach ($posts as $post) {
        //         $dataToInsert[] = [
        //             'title' => $post->post_title,
        //             'content' => $post->post_content,
        //             'created_at' => $post->post_date,
        //             'updated_at' => now(),
        //         ];
        //     }

        //     /* Insert into Laravel DB */
        //     DB::table('posts')->insert($dataToInsert);
        //     $offset += $batchSize;

        //     $this->command->info("Processed {$offset} records...");
        // }

        // $this->command->info('Migration completed!');
    }
}
