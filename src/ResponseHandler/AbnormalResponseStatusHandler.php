<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\AbnormalHttpStatus;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use Psr\Http\Message\ResponseInterface;

class AbnormalResponseStatusHandler implements ResponseHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ResponseInterface $response, string $operationId): AbnormalHttpStatus
    {
        if (!($response->getStatusCode() >= 200 && $response->getStatusCode() <= 300)) {
            return new AbnormalHttpStatus([
                'statusCode' => $response->getStatusCode(),
                'contents' => $response->getBody()->getContents()
            ]);
        }

        throw new UndefinedResponseException($operationId, $response->getStatusCode());
    }
}
