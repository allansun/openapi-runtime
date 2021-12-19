<?php

namespace OpenAPI\Runtime\Tests\Fixtures\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\ResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class InvalidResponseHandler implements ResponseHandlerInterface
{
    public function __invoke(ResponseInterface $response, string $operationId): ?ModelInterface
    {
        throw new UndefinedResponseException($operationId, $response->getStatusCode());
    }
}
