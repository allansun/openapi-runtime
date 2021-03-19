<?php

namespace OpenAPI\Runtime\Tests\Fixtures\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\ResponseHandlerInterface;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;

class DummyResponseHandler implements ResponseHandlerInterface
{

    public function __invoke($response, string $operationId): ModelInterface
    {
        return new TestModel();
    }
}
