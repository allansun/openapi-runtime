<?php


namespace OpenAPI\Runtime;


interface OpenApiClientInterface
{
    /**
     * @param  string  $operationId
     * @param  string  $method
     * @param  string  $uri
     * @param  array   $options
     *
     * @return ModelInterface|ModelInterface[]|mixed
     */
    public function request(string $operationId, string $method, string $uri, array $options = []);

    public static function getInstance(): OpenApiClientInterface;
}