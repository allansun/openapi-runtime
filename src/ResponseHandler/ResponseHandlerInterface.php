<?php


namespace OpenApiRuntime\ResponseHandler;


use OpenApiRuntime\ModelInterface;
use OpenApiRuntime\ResponseHandler\Exception\UnparsableException;

interface ResponseHandlerInterface
{
    /**
     * @param          $response
     * @param  string  $operationId
     *
     * @return ModelInterface
     * @throws \OpenApiRuntime\ResponseHandler\Exception\UnparsableException
     */
    public function __invoke($response,string $operationId): ModelInterface;
}