<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime;

use Http\Discovery\Psr17FactoryDiscovery;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStackInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

abstract class AbstractAPI implements APIInterface
{
    /**
     * @var string|ResponseHandlerStackInterface
     */
    protected static $responseHandlerStack;

    /**
     * @var ClientInterface
     */
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        if (!static::$responseHandlerStack instanceof ResponseHandlerStackInterface) {
            static::$responseHandlerStack = new static::$responseHandlerStack();
        }
    }

    /**
     * @param  ResponseHandlerStackInterface  $responseHandlerStack
     */
    public static function setResponseHandlerStack(ResponseHandlerStackInterface $responseHandlerStack): void
    {
        self::$responseHandlerStack = $responseHandlerStack;
    }

    /**
     * @param  string                                $operationId
     * @param  string                                $method
     * @param  string|UriInterface                   $uri
     * @param  array                                 $headers
     * @param  resource|string|StreamInterface|null  $body
     * @param  string
     *
     * @return ModelInterface|ModelInterface[]|mixed
     */
    protected function request(
        string $operationId,
        string $method,
        $uri,
        $body = null,
        array $headers = [],
        string $protocol = '1.1'
    ) {
        $messageFactory = Psr17FactoryDiscovery::findRequestFactory();

        $request = $messageFactory->createRequest($method, $uri)->withBody($body)->withProtocolVersion($protocol);

        foreach ($headers as $name => $value) {
            $request->withHeader($name, $value);
        }

        return static::$responseHandlerStack->handle($this->client->sendRequest($request), $operationId);
    }
}