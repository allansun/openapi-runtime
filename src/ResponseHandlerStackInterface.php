<?php


namespace OpenApiRuntime;


interface ResponseHandlerStackInterface
{
    public function handle($response, string $operationId): ModelInterface;
}