<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Stories\Models\Story;
use Illuminate\Database\Seeder;

/**
 * Optional local/staging content for Latest Stories (run manually).
 *
 * php artisan db:seed --class=Database\\Seeders\\StoriesPlaceholderSeeder
 */
class StoriesPlaceholderSeeder extends Seeder
{
    public function run(): void
    {
        Story::create([
            'title' => 'Donate to Al-Ayn\'s COVID-19 Relief Fund',
            'slug' => 'covid-19-relief-fund',
            'excerpt' => '<p>Support families in need through Al-Ayn.</p>',
            'body' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n\nSecond paragraph for readability.",
            'hero_image_url' => 'https://nawhas.s3.us-east-2.amazonaws.com/stories/alayn-charity.jpg',
            'display_date' => '2020-05-05',
            'published' => true,
        ]);
    }
}
