<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Lyrics\Documents;

use App\Modules\Lyrics\Documents\JsonV1\Document;
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

        $this->assertJsonStringEqualsJsonString($expected, Document::make()->getContent());
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

        $this->assertJsonStringEqualsJsonString($expected, $doc->getContent());
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

    /**
     * @test
     * @dataProvider provideContentsForEmptyTest
     * @throws \JsonException
     */
    public function it_determines_empty_documents_correctly(string $content, bool $expected): void
    {

        $doc = Document::fromJson($content);

        $this->assertEquals($expected, $doc->isEmpty());
    }

    public static function provideContentsForEmptyTest(): array
    {
        return [
            'empty json' => ['{}', true],
            'empty document with timestamps enabled' => [
                /** @lang JSON */ <<<JSON
                {
                  "meta": {
                    "timestamps": true
                  },
                  "data": [
                    {
                      "timestamp": 0,
                      "lines": [
                        {
                          "text": "",
                          "repeat": 0
                        }
                      ]
                    }
                  ]
                }
                JSON,
                            true,
                        ],
                        'empty document with timestamps disabled' => [
                            /** @lang JSON */ <<<JSON
                {
                  "meta": {
                    "timestamps": false
                  },
                  "data": [
                    {
                      "timestamp": 0,
                      "lines": [
                        {
                          "text": "",
                          "repeat": 0
                        }
                      ]
                    }
                  ]
                }
                JSON,
                            true,
                        ],
                        'lots_of_empty_document_lines' => [
                            /** @lang JSON */ <<<JSON
                {
                  "meta": {
                    "timestamps": false
                  },
                  "data": [
                    {
                      "timestamp": 0,
                      "lines": [
                        {
                          "text": "",
                          "repeat": 0
                        },
                        {
                          "text": "",
                          "repeat": 0
                        },
                        {
                          "text": "\\t\\t\\t",
                          "repeat": 0
                        },
                        {
                          "text": "\\t",
                          "repeat": 0
                        }
                      ]
                    },
                    {
                      "timestamp": 0,
                      "lines": [
                        {
                          "text": "    ",
                          "repeat": 0
                        },
                        {
                          "text": "     ",
                          "repeat": 0
                        }
                      ]
                    }
                  ]
                }
                JSON,
                            true,
                        ],
                        'not empty' => [
                            /** @lang JSON */ <<<JSON
                {
                  "meta": {
                    "timestamps": true
                  },
                  "data": [
                    {
                      "timestamp": 0,
                      "lines": [
                        {
                          "text": "h",
                          "repeat": 0
                        }
                      ]
                    },
                    {
                      "timestamp": 0,
                      "lines": [
                        {
                          "text": "    ",
                          "repeat": 0
                        },
                        {
                          "text": "     ",
                          "repeat": 0
                        }
                      ]
                    }
                  ]
                }
                JSON,
                            false,
                        ],
                    ];
                }
}
