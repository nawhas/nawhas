<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Tests\Unit\Documents;

use App\Modules\Lyrics\Documents\PlainText\Document;
use App\Tests\TestCase;

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

    public function provideContentsForEmptyTest(): array
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
