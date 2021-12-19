<?php

namespace OpenAPI\Runtime\Tests\Fixtures\ResponseHandler;

use OpenAPI\Runtime\ResponseHandler\ResponseHandlerInterface;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
use Psr\Http\Message\ResponseInterface;

class DummyResponseHandler implements ResponseHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ResponseInterface $response, string $operationId): TestModel
    {
        return new TestModel();
    }
}
