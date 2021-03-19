<?php

namespace OpenAPI\Runtime\Tests\Fixtures\ResponseHandler;

use OpenAPI\Runtime\ResponseHandler\ResponseHandlerInterface;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;

class DummyResponseHandler implements ResponseHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke($response, string $operationId)
    {
        return new TestModel();
    }
}
