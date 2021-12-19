<?php

namespace OpenAPI\Runtime\Tests\Fixtures;

use OpenAPI\Runtime\AbstractAPI;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;

class TestAPI extends AbstractAPI
{
    protected ?string $responseHandlerStackClass = ResponseHandlerStack::class;

    public function getById($id): ?TestModel
    {
        return $this->request('testApiGetById', 'GET', '/test');
    }
}
