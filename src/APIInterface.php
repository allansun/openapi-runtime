<?php

namespace OpenAPI\Runtime;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface APIInterface
{
    /**
     * @param  string                                      $operationId
     * @param  string                                      $method
     * @param  string|UriInterface                         $uri
     * @param  array|resource|string|StreamInterface|null  $body
     * @param  array<string,mixed>                         $queries
     * @param  array                                       $headers
     * @param  string                                      $protocol
     *
     * @return ModelInterface|ModelInterface[]|mixed
     * @throws ClientExceptionInterface
     */
    public function request(
        string $operationId,
        string $method,
        $uri,
        $body = null,
        array $queries = [],
        array $headers = [],
        string $protocol = '1.1'
    );
}
