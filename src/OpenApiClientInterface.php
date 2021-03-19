<?php


namespace OpenAPI\Runtime;


interface OpenApiClientInterface
{
    public function request(string $operationId, string $method, string $uri, array $options = []): ModelInterface;

    public static function getInstance(): OpenApiClientInterface;
}