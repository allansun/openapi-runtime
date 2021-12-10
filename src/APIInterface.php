<?php

namespace OpenAPI\Runtime;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface APIInterface
{
    public function request(
        string $operationId,
        string $method,
        UriInterface|string $uri,
        StreamInterface|array|string $body = null,
        array $queries = [],
        array $headers = [],
        string $protocol = '1.1'
    ): array|ModelInterface|null;
}
