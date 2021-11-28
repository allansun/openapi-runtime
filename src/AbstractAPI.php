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
use Http\Discovery\Psr18ClientDiscovery;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStackInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
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

    /**
     * @var UriFactoryInterface
     */
    protected $uriFactory;

    /**
     * @var RequestFactoryInterface
     */
    protected $requestFactory;
    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    public function __construct(?ClientInterface $client = null)
    {
        if (null === $client) {
            $client = Psr18ClientDiscovery::find();
        }

        $this->client         = $client;
        $this->uriFactory     = Psr17FactoryDiscovery::findUriFactory();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory  = Psr17FactoryDiscovery::findStreamFactory();

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
     * @param  string                                      $operationId
     * @param  string                                      $method
     * @param  string|UriInterface                         $uri
     * @param  array|resource|string|StreamInterface|null  $body
     * @param  array<string,mixed>                         $queries
     * @param  array                                       $headers
     * @param  string                                      $protocol
     *
     * @return ModelInterface|ModelInterface[]|mixed
     * @throws ClientExceptionInterface
     */
    protected function request(
        string $operationId,
        string $method,
        $uri,
        $body = null,
        array $queries = [],
        array $headers = [],
        string $protocol = '1.1'
    ) {
        if (!$uri instanceof UriInterface) {
            $queryStrings = [];
            foreach ($queries as $key => $value) {
                $queryStrings[] = urlencode($key) . '=' . urlencode($value);
            }
            $uri = $this->uriFactory->createUri($uri)->withQuery(implode('&', $queryStrings));
        }

        if (!$body instanceof StreamInterface) {
            if (is_array($body)) {
                $body = json_encode($body);
            }
            $body = $this->streamFactory->createStream($body);
        }

        $request = $this->requestFactory
            ->createRequest($method, $uri)
            ->withBody($body)
            ->withProtocolVersion($protocol);

        foreach ($headers as $name => $value) {
            $request->withHeader($name, $value);
        }

        return static::$responseHandlerStack->handle(
            $this->client->sendRequest($request),
            $operationId
        );
    }
}