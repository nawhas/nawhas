<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Lyrics\Documents;

use App\Modules\Lyrics\Documents\JsonV1\Document;
use Illuminate\Validation\ValidationException;
use JsonException;
use Tests\TestCase;

class JsonV1DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function it_serializes_to_json_properly(): void
    {
        $expected = /** @lang JSON */ <<<JSON
        {
           "meta": {
               "timestamps": true
           },
           "data": []
        }
        JSON;

        $this->assertJsonStringEqualsJsonString($expected, Document::make()->toJson());
    }

    /**
     * @test
     */
    public function it_parses_json_source_content(): void
    {
        $source = /** @lang JSON */ <<<JSON
        {
           "meta": {
               "timestamps": true
           },
           "data": [
               {
                   "timestamp": 0,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 },
                        { "text": "some text goes here", "repeat": 4 }
                   ],
                   "type": "normal"
               },
               {
                   "timestamp": null,
                   "lines": [
                        { "text": "", "repeat": 0 }
                   ],
                   "type": "spacer"
               },
               {
                   "timestamp": 14.04,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 }
                   ]
               },
               {
                   "timestamp": 19.044425,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 }
                   ]
               }
           ]
        }
        JSON;

        $expected = /** @lang JSON */ <<<JSON
        {
           "meta": {
               "timestamps": true
           },
           "data": [
               {
                   "timestamp": 0,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 },
                        { "text": "some text goes here", "repeat": 4 }
                   ],
                   "type": "normal"
               },
               {
                   "timestamp": null,
                   "lines": [
                        { "text": "", "repeat": 0 }
                   ],
                   "type": "spacer"
               },
               {
                   "timestamp": 14.04,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 }
                   ],
                   "type": "normal"
               },
               {
                   "timestamp": 19.044425,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 }
                   ],
                   "type": "normal"
               }
           ]
        }
        JSON;

        $doc = Document::fromJson($source);

        $this->assertJsonStringEqualsJsonString($expected, $doc->toJson());
    }

    /**
     * @test
     */
    public function it_renders_to_string_properly(): void
    {

        $source = /** @lang JSON */ <<<JSON
        {
           "meta": {
               "timestamps": true
           },
           "data": [
               {
                   "timestamp": 0,
                   "lines": [
                        { "text": "Hello", "repeat": 0 },
                        { "text": "World", "repeat": 2 }
                   ],
                   "type": "normal"
               },
               {
                   "timestamp": null,
                   "lines": [
                        { "text": "", "repeat": 0 }
                   ],
                   "type": "spacer"
               },
               {
                   "timestamp": 14.04,
                   "lines": [
                        { "text": "This is a test", "repeat": 0 },
                        { "text": "Remain calm", "repeat": 2 }
                   ]
               }
           ]
        }
        JSON;

        $expected = <<<TEXT
        Hello
        World (x2)
        
        This is a test
        Remain calm (x2)
        TEXT;

        $doc = Document::fromJson($source);

        $this->assertEquals($expected, $doc->render());
    }
}
