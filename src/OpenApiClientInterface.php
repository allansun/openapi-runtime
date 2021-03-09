<?php


namespace OpenApiRuntime;


interface OpenApiClientInterface
{
    public function request(string $operationId, string $method, string $uri, array $options = []): ModelInterface;

    public static function getInstance(): OpenApiClientInterface;
}