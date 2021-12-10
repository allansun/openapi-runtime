<?php

namespace OpenAPI\Runtime\Tests\Fixtures;

use OpenAPI\Runtime\AbstractAPI;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStackInterface;

class TestAPI extends AbstractAPI
{
    protected static ResponseHandlerStackInterface|string $responseHandlerStack = ResponseHandlerStack::class;

    public function getById($id): ?TestModel
    {
        return $this->request('testApiGetById', 'GET', '/test');
    }
}
