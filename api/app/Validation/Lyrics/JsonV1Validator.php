<?php

declare(strict_types=1);

namespace App\Validation\Lyrics;

use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class JsonV1Validator
{
    private ValidationFactory $validation;
    // private MessageBag $errors;

    public function __construct(ValidationFactory $validation)
    {
        // $this->errors = new MessageBag();
        $this->validation = $validation;
    }


    /**
     * @throws \JsonException
     */
    public function validate(string $content): bool
    {
        // $this->errors = new MessageBag();
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $this->getValidator($data)->validate();

        return true;
    }


    /*private function customValidation(array $data): void
    {
        $this->validateMeta($data);

        if ($this->errors->any()) {
            throw ValidationException::withMessages($this->errors->messages());
        }
    }

    private function validateMeta(array $data): void
    {
        if (!Arr::has($data, 'meta')) {
            $this->errors->add('meta', trans('validation.present', ['attribute' => 'meta']));
            return;
        }

        if (Arr::has($data, 'meta.timestamps')) {
            $timestamps = Arr::get($data, 'meta.timestamps');
        }
    }*/

    private function getValidator(array $json): Validator
    {
        $rules = [
            // Meta
            'meta' => ['present', 'array'],
            'meta.timestamps' => ['boolean'],
            // Data Array
            'data' => ['present', 'array'],
            // Group Objects
            'data.*.timestamp' => ['numeric'],
            'data.*.lines' => ['required', 'array'],
            'data.*.type' => ['string', 'in:normal,spacer'],
            // Lines
            'data.*.lines.*.text' => ['string'],
            'data.*.lines.*.repeat' => ['integer', 'min:0', 'max:20'],
        ];

        return $this->validation->make($json, $rules);
    }
}
