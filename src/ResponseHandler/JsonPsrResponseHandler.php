<?php

namespace OpenApiRuntime\ResponseHandler;

use OpenApiRuntime\ModelInterface;
use OpenApiRuntime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenApiRuntime\ResponseHandler\Exception\UndefinedResponseException;
use OpenApiRuntime\ResponseHandler\Exception\UnparsableException;
use OpenApiRuntime\ResponseTypes;
use Psr\Http\Message\ResponseInterface;

class JsonPsrResponseHandler implements ResponseHandlerInterface
{
    public function __invoke($response, string $operationId): ModelInterface
    {
        if ($response instanceof ResponseInterface) {
            return $this->invoke($response, $operationId);
        }

        throw new IncompatibleResponseException(sprintf(
            '%s cannot handle %s response',
            self::class,
            get_class($response)
        ));
    }

    /** @noinspection DuplicatedCode */
    private function invoke(ResponseInterface $response, string $operationId): ModelInterface
    {
        $contents = json_decode((string)$response->getBody(), true);

        if (!is_array($contents)) {
            throw new UnparsableException('Response is not a valid Json');
        }

        if (array_key_exists($operationId, ResponseTypes::getTypes()) &&
            array_key_exists($response->getStatusCode() . '.', ResponseTypes::getTypes()[$operationId])) {
            $className = ResponseTypes::getTypes()[$operationId][$response->getStatusCode() . '.'];

            return new $className($contents);
        }

        throw new UndefinedResponseException(sprintf("Operation '%s' dose not have a defined response.", $operationId));
    }
}
