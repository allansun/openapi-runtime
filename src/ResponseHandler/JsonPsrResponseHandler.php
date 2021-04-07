<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\ResponseTypesInterface;
use Psr\Http\Message\ResponseInterface;

class JsonPsrResponseHandler implements ResponseHandlerInterface, ResponseTypesInjecableInterface
{
    private static ResponseTypesInterface $responseTypes;

    public function __invoke($response, string $operationId)
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

    /**
     * @param  ResponseInterface  $response
     * @param  string             $operationId
     *
     * @return ModelInterface|ModelInterface[]
     * @throws UndefinedResponseException
     * @throws UnparsableException
     * @noinspection DuplicatedCode
     */
    private function invoke(ResponseInterface $response, string $operationId)
    {
        $contents = json_decode((string)$response->getBody(), true);

        if (!is_array($contents)) {
            throw new UnparsableException('Response is not a valid Json');
        }

        if (array_key_exists($operationId, $this->getResponseTypes()::getTypes()) &&
            array_key_exists($response->getStatusCode() . '.', $this->getResponseTypes()::getTypes()[$operationId])) {
            $className = $this->getResponseTypes()::getTypes()[$operationId][$response->getStatusCode() . '.'];
            if ($className != rtrim($className, '[]')) {
                $className = rtrim($className, '[]');
                $results   = [];
                foreach ($contents as $content) {
                    $results[] = new $className($content);
                }

                return $results;
            } else {
                return new $className($contents);
            }
        }

        throw new UndefinedResponseException(sprintf("Operation '%s' dose not have a defined response.", $operationId));
    }

    public function getResponseTypes(): ResponseTypesInterface
    {
        if (empty(self::$responseTypes)) {
            self::$responseTypes = new ResponseTypes();
        }

        return self::$responseTypes;
    }

    static public function setResponseTypes(ResponseTypesInterface $responseTypes): self
    {
        self::$responseTypes = $responseTypes;

        return new self();
    }
}
