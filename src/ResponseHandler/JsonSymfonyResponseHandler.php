<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseTypes;
use Symfony\Contracts\HttpClient\ResponseInterface;

class JsonSymfonyResponseHandler implements ResponseHandlerInterface
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
        $contents = json_decode((string)$response->getContent(false), true);

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
