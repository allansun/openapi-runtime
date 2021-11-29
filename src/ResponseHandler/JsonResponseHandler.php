<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\ResponseTypesInterface;
use Psr\Http\Message\ResponseInterface;

class JsonResponseHandler implements ResponseHandlerInterface, ResponseTypesInjecableInterface
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

    /**
     * @param  ResponseInterface  $response
     * @param  string             $operationId
     *
     * @return ModelInterface|ModelInterface[]
     * @throws UndefinedResponseException
     * @throws UnparsableException
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

        throw new UndefinedResponseException(
            sprintf("Operation '%s' dose not have a defined response when status code is %s",
                $operationId, $response->getStatusCode()));
    }
}
