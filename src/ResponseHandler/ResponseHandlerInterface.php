<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseHandlerInterface
{
    /**
     * @param  ResponseInterface  $response
     * @param  string             $operationId
     *
     * @return array|ModelInterface|null
     */
    public function __invoke(
        ResponseInterface $response,
        string $operationId
    );
}
