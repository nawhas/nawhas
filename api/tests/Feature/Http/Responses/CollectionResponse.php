<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

class CollectionResponse extends Response
{
    protected function getJsonStructure(): array
    {
        return [
            'data',
            'meta'
        ];
    }
}
