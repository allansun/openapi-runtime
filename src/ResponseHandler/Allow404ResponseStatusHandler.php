<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use Psr\Http\Message\ResponseInterface;

class Allow404ResponseStatusHandler implements ResponseHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ResponseInterface $response, string $operationId)
    {
        if (404 == $response->getStatusCode()) {
            return null;
        }

        throw new UndefinedResponseException($operationId, $response->getStatusCode());
    }
}
