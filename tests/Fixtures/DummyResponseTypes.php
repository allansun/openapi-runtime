<?php

namespace OpenAPI\Runtime\Tests\Fixtures;

use OpenAPI\Runtime\ResponseTypes;

class DummyResponseTypes extends ResponseTypes
{
    /**
     * @psalm-var array{a: 'b', testApiGetById: array{'200.': TestModel::class}}
     */
    public array $types = [
        'a' => 'b',
        'testApiGetById' => [
            '200.' => TestModel::class
        ]
    ];
}
