<?php

namespace Tests\Unit\Console\Helpers;

use App\Console\Helpers\WriteUpCleaner;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class WriteUpCleanerTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideLyrics
     */
    public function it_cleans_up_lyrics($source, $clean): void
    {
        $this->assertEquals($clean, (new WriteUpCleaner())->cleanup($source));
    }

    public function provideLyrics(): array
    {
        $data = [];
        $fs = new Filesystem();

        foreach ($fs->directories(__DIR__ . '/../../../Fixtures/WriteUpCleaner') as $group) {
            $name = $fs->basename($group);
            $data[$name] = [
                'source' => $fs->get("$group/source.txt"),
                'clean' => $fs->get("$group/clean.txt"),
            ];
        }

        return $data;
    }
}
