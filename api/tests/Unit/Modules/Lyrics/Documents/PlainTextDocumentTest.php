<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Lyrics\Documents;

use App\Modules\Lyrics\Documents\PlainText\Document;
use Tests\TestCase;

class PlainTextDocumentTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideContentsForEmptyTest
     */
    public function it_determines_empty_documents_correctly(string $contents, bool $expected): void
    {
        $doc = new Document($contents);

        $this->assertEquals($expected, $doc->isEmpty());
    }

    public static function provideContentsForEmptyTest(): array
    {
        return [
            'empty string' => ['', true],
            'single line whitespace' => ['          ', true],
            'multi-line whitespace' => [
                <<<STR



                STR,
                true
            ],
            'not empty' => ['       . ', false],
        ];
    }
}
