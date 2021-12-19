<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseHandlerInterface
{
    /** @psalm-suppress LessSpecificReturnType */
    public function __invoke(
        ResponseInterface $response,
        string $operationId
    ): array|ModelInterface|null;
}
