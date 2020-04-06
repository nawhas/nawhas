<?php

declare(strict_types=1);

namespace Tests\Unit\Validation\Lyrics;

use App\Validation\Lyrics\JsonV1Validator;
use Illuminate\Validation\ValidationException;
use JsonException;
use Tests\TestCase;

class JsonV1ValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_error_on_invalid_json(): void
    {
        $content = 'iau43908fn';

        $validator = app(JsonV1Validator::class);

        $this->expectException(JsonException::class);
        $validator->validate($content);
    }

    /**
     * @test
     */
    public function it_accepts_bare_minimum_json_structure(): void
    {
        $content = json_encode([
            'meta' => (object)[],
            'data' => [],
        ]);

        $validator = app(JsonV1Validator::class);

        try {
            $this->assertTrue($validator->validate($content));
        } catch (ValidationException $e) {
            $this->fail("Validation failed\n" . json_encode($e->errors(), JSON_PRETTY_PRINT));
        }
    }

    /**
     * @test
     * @dataProvider provideBadData
     */
    public function it_properly_throws_validation_exceptions(string $content, array $errors): void
    {
        $validator = app(JsonV1Validator::class);

        try {
            $validator->validate($content);
            $this->fail('The validation did not fail as expected.');
        } catch (ValidationException $e) {
            foreach ($errors as $attribute => $messages) {
                $this->assertEqualsCanonicalizing(
                    collect($messages)->map(fn ($msg) => trans($msg, ['attribute' => $attribute]))->toArray(),
                    $e->errors()[$attribute],
                );
            }
        }
    }

    public function provideBadData(): array
    {
        return [
            'no meta field' => [
                /** @lang JSON */
                <<<JSON
                {
                    "data": []
                }
                JSON,
                [
                    'meta' => [
                        'validation.present',
                    ]
                ]
            ],
            'invalid meta.timestamps' => [
                /** @lang JSON */
                <<<JSON
                {
                    "meta": {
                        "timestamps": "hello"
                    },
                    "data": []
                }
                JSON,
                [
                    'meta.timestamps' => [
                        'validation.boolean',
                    ]
                ]
            ],
            'invalid data.*.timestamp' => [
                /** @lang JSON */
                <<<JSON
                {
                    "meta": {
                        "timestamps": true
                    },
                    "data": [
                        {
                            "timestamp": "hello",
                            "lines": [
                                { "text": "Test", "repeat": 2 }
                            ]
                        }
                    ]
                }
                JSON,
                [
                    'data.0.timestamp' => [
                        'validation.numeric',
                    ]
                ]
            ]
        ];
    }
}
