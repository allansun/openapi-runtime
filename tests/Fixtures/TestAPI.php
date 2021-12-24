<?php

namespace OpenAPI\Runtime\Tests\Fixtures;

use OpenAPI\Runtime\AbstractAPI;
use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;

class TestAPI extends AbstractAPI
{
    protected string $responseHandlerStackClass = ResponseHandlerStack::class;

    public function getById(int $id): array|ModelInterface|null
    {
        return $this->request('testApiGetById', 'GET', '/test');
    }
}
