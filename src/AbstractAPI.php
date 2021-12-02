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

    public function request(
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
                if (is_array($value)) {
                    //$queries will be ['id[]'=>[1,2,3]], encode to id%5B%5D=1&id%5B%5D=2&id%5B%5D=3
                    $queryStrings[] = urlencode($key) . '=' . implode('&' . urlencode($key) . '=', $value);
                } else {
                    $queryStrings[] = urlencode($key) . '=' . urlencode($value);
                }
            }
            $uri = $this->uriFactory->createUri($uri)->withQuery(implode('&', $queryStrings));
        }

        if (!$body instanceof StreamInterface && !is_null($body)) {
            if (is_array($body)) {
                $body = json_encode($body);
            }
            $body = $this->streamFactory->createStream($body);
        }

        $request = $this->requestFactory
            ->createRequest($method, $uri)
            ->withProtocolVersion($protocol);

        if ($body) {
           $request = $request->withBody($body);
        }

        foreach ($headers as $name => $value) {
           $request = $request->withHeader($name, $value);
        }

        return static::$responseHandlerStack->handle(
            $this->client->sendRequest($request),
            $operationId
        );
    }
}
