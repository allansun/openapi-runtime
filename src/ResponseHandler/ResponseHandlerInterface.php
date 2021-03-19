<?php


namespace OpenAPI\Runtime\ResponseHandler;


use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;

interface ResponseHandlerInterface
{
    /**
     * @param          $response
     * @param  string  $operationId
     *
     * @return ModelInterface|ModelInterface[]
     * @throws UnparsableException
     */
    public function __invoke($response, string $operationId);
}