<?php

namespace OpenApiRuntime\Tests\Fixtures\ResponseHandler;

use OpenApiRuntime\ModelInterface;
use OpenApiRuntime\ResponseHandler\ResponseHandlerInterface;
use OpenApiRuntime\Tests\Fixtures\TestModel;

class DummyResponseHandler implements ResponseHandlerInterface
{

    public function __invoke($response, string $operationId): ModelInterface
    {
        return new TestModel();
    }
}
