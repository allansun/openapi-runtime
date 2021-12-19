<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\ResponseHandlerThrowable;
use Psr\Http\Message\ResponseInterface;

interface ResponseHandlerInterface
{
    /**
     * @param  ResponseInterface  $response
     * @param  string             $operationId
     *
     * @return ModelInterface|ModelInterface[]|null
     * @throws ResponseHandlerThrowable
     */
    public function __invoke(
        ResponseInterface $response,
        string $operationId
    ): array|ModelInterface|null;
}
