<?php

namespace App\Seeders;

use Faker\Factory;
use WP_CLI;

class PostSeeder
{
    /**
     * Seed posts with dummy data.
     *
     * @return void
     */
    public function __invoke()
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $post_data = [
                'post_title'   => $faker->sentence,
                'post_content' => $faker->paragraphs(15, true),
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_type'    => 'post',
            ];

            $post_id = wp_insert_post($post_data);
        }

        WP_CLI::success('Posts seeded successfully.');
    }
}
