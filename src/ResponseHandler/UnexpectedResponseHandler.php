<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\UnexpectedResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * UnexpectedResponseHandler should be your last handler to run in stack,
 * if you don't any exception to be thrown when ResponseTypes doesn't define how to deal with a
 * particular reponse.
 * This handler is purely optional.
 */
class UnexpectedResponseHandler implements ResponseHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ResponseInterface $response, string $operationId): UnexpectedResponse
    {
        return new UnexpectedResponse([
            'statusCode' => $response->getStatusCode(),
            'contents' => $response->getBody()->getContents()
        ]);
    }
}
