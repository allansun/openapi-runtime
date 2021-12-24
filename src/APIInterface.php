<?php

namespace OpenAPI\Runtime;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface APIInterface
{
    /**
     * @param  string                             $operationId
     * @param  string                             $method
     * @param  UriInterface|string                $uri
     * @param  StreamInterface|array|string|null  $body
     * @param  array                              $queries
     * @param  array                              $headers
     * @param  string                             $protocol
     *
     * @return array|ModelInterface|null
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
