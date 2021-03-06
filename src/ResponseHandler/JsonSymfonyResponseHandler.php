<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\ResponseTypesInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class JsonSymfonyResponseHandler implements ResponseHandlerInterface, ResponseTypesInjecableInterface
{
    /**
     * @var ResponseTypesInterface
     */
    private $responseTypes;

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
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @noinspection DuplicatedCode
     */
    private function invoke(ResponseInterface $response, string $operationId)
    {
        $contents = json_decode($response->getContent(false), true);

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
        if (empty($this->responseTypes)) {
            $this->responseTypes = new ResponseTypes();
        }

        return $this->responseTypes;
    }

    public function setResponseTypes(ResponseTypesInterface $responseTypes): ResponseTypesInjecableInterface
    {
        $this->responseTypes = $responseTypes;

        return $this;
    }
}
