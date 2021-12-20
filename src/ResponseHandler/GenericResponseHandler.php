<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\GenericResponse;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use Psr\Http\Message\ResponseInterface;

class GenericResponseHandler implements ResponseHandlerInterface, ResponseTypesInjecableInterface
{
    use ResponseTypesInjecableTrait;

    public function __invoke(ResponseInterface $response, string $operationId): GenericResponse
    {
        if (
            array_key_exists($operationId, $this->getResponseTypes()::getTypes()) &&
            array_key_exists($response->getStatusCode() . '.', $this->getResponseTypes()::getTypes()[$operationId])
        ) {
            return new GenericResponse([
                'statusCode' => $response->getStatusCode(),
                'contents' => $response->getBody()->getContents(),
            ]);
        }

        throw new UndefinedResponseException($operationId, $response->getStatusCode());
    }
}
