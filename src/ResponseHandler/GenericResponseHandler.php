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
        $responseTypes = $this->getResponseTypes()::getTypes();
        if (
            array_key_exists($operationId, $responseTypes) &&
            array_key_exists($response->getStatusCode() . '.', $responseTypes[$operationId]) &&
            GenericResponse::class == $responseTypes[$operationId][$response->getStatusCode() . '.']
        ) {
            return new GenericResponse([
                'statusCode' => $response->getStatusCode(),
                'contents' => $response->getBody()->getContents(),
            ]);
        }

        throw new UndefinedResponseException($operationId, $response->getStatusCode());
    }
}
