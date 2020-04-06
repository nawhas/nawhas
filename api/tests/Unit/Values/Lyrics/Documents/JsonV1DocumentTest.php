<?php

declare(strict_types=1);

namespace Tests\Unit\Values\Lyrics\Documents;

use App\Values\Lyrics\Documents\JsonV1\Document;
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
        $expected = <<<JSON
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
        $source = <<<JSON
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

        $expected = <<<JSON
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
    public function it_handles_missing_attributes(): void
    {
        $source = <<<JSON
        {
           "meta": {},
           "data": [
               {
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 },
                        {  }
                   ]
               },
               {
                   "timestamp": null,
                   "lines": [
                        { "text": "", "repeat": 0 }
                   ],
                   "type": "spacer"
               },
               {
                   "timestamp": "whatiswrongwithyou>>!>!?#",
                   "lines": [
                        { "text": true, "repeat": -2394 },
                        { "text": "some text goes here", "repeat": "yes" },
                        { "text": 10e10, "repeat": "yes" }
                   ]
               },
               {
                   "timestamp": "19.0000.044425",
                   "lines": []
               },
               {
                   "timestamp": 10e10,
                   "lines": []
               }
           ]
        }
        JSON;

        $this->expectException(ValidationException::class);
        $doc = Document::fromJson($source);
    }
}
