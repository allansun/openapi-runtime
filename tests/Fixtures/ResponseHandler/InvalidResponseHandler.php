<?php

namespace OpenAPI\Runtime\Tests\Fixtures\ResponseHandler;

use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\ResponseHandlerInterface;

class InvalidResponseHandler implements ResponseHandlerInterface
{

    public function __invoke($response, string $operationId)
    {
        throw new UndefinedResponseException();
    }
}
