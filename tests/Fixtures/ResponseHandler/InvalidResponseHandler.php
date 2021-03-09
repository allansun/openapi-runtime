<?php

namespace OpenApiRuntime\Tests\Fixtures\ResponseHandler;

use OpenApiRuntime\ModelInterface;
use OpenApiRuntime\ResponseHandler\Exception\UndefinedResponseException;
use OpenApiRuntime\ResponseHandler\ResponseHandlerInterface;

class InvalidResponseHandler implements ResponseHandlerInterface
{

    public function __invoke($response, string $operationId): ModelInterface
    {
        throw new UndefinedResponseException();
    }
}
