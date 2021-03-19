<?php


namespace OpenAPI\Runtime;


interface ResponseHandlerStackInterface
{
    public function handle($response, string $operationId): ModelInterface;
}