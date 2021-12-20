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
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use Psr\Http\Message\ResponseInterface;

class JsonResponseHandler implements ResponseHandlerInterface, ResponseTypesInjecableInterface
{
    use ResponseTypesInjecableTrait;

    /** @psalm-suppress MoreSpecificReturnType */
    public function __invoke(ResponseInterface $response, string $operationId): ModelInterface|array
    {
        $contents = json_decode($response->getBody()->getContents(), true);

        if (!is_array($contents)) {
            throw new UnparsableException('Response is not a valid Json');
        }

        if (
            array_key_exists($operationId, $this->getResponseTypes()::getTypes()) &&
            array_key_exists($response->getStatusCode() . '.', $this->getResponseTypes()::getTypes()[$operationId])
        ) {
            $className = $this->getResponseTypes()::getTypes()[$operationId][$response->getStatusCode() . '.'];
            if ($className != rtrim($className, '[]')) {
                $className = rtrim($className, '[]');
                $results   = [];
                foreach ($contents as $content) {
                    $results[] = new $className($content);
                }

                return $results;
            } else {
                /** @psalm-suppress LessSpecificReturnStatement */
                return new $className($contents);
            }
        }

        throw new UndefinedResponseException($operationId, $response->getStatusCode());
    }
}
