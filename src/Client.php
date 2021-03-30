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

use GuzzleHttp;
use GuzzleHttp\Psr7\Request;
use OpenAPI\Runtime\Exception\CommonException;
use OpenAPI\Runtime\Exception\IncompatibleTransportClientException;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as SymfonyResponseInterface;

class Client implements OpenApiClientInterface
{
    static private ?Client $instance;
    /**
     * @var PsrClientInterface|SymfonyHttpClientInterface
     */
    private $client;
    private array $defaultOptions;
    private ResponseHandlerStackInterface $responseHandlerStack;

    /**
     * Client constructor.
     *
     * @param  SymfonyHttpClientInterface|PsrClientInterface|null  $client
     * @param  ResponseHandlerStackInterface|null                  $responseHandlerStack
     * @param  array                                               $defaultOptions
     */
    private function __construct(
        $client = null,
        ?ResponseHandlerStackInterface $responseHandlerStack = null,
        array $defaultOptions = []
    ) {
        $this->defaultOptions = $defaultOptions;

        if ($client) {
            if (!($client instanceof SymfonyHttpClientInterface) && !($client instanceof PsrClientInterface)) {
                throw new IncompatibleTransportClientException();
            }
            $this->client = $client;
        } else {
            // Create a default client
            $this->client = $this->getDefaultClient();
        }

        $this->setResponseHandlerStack(
            $this->responseHandlerStack ??
            $responseHandlerStack ??
            new DefaultResponseHandlerStack()
        );

    }

    private function getDefaultClient(): GuzzleHttp\Client
    {
        $HandlerStack = $this->defaultOptions['handler'] ?? GuzzleHttp\HandlerStack::create();

        // Setup logging bit
        $HandlerStack->push(
            GuzzleHttp\Middleware::log(
                Logger::getInstance()->getLogger(),
                new GuzzleHttp\MessageFormatter('{method} Response: {res_body}'),
                LogLevel::DEBUG
            )
        );

        $HandlerStack->push(
            GuzzleHttp\Middleware::log(
                Logger::getInstance()->getLogger(),
                new GuzzleHttp\MessageFormatter('{method} : {uri} - Request: {req_body}'),
                LogLevel::DEBUG
            )
        );

        $this->defaultOptions['handler'] = $HandlerStack;

        return new GuzzleHttp\Client($this->defaultOptions);
    }

    public function setResponseHandlerStack(ResponseHandlerStackInterface $responseHandlerStack): self
    {
        $this->responseHandlerStack = $responseHandlerStack;

        return $this;
    }

    public static function configure(
        $client = null,
        ?ResponseHandlerStackInterface $responseHandlerStack = null,
        array $defaultOptions = []
    ): Client {
        self::$instance = new self($client, $responseHandlerStack, $defaultOptions);

        return self::$instance;
    }

    public static function getInstance(): Client
    {
        if (isset(self::$instance)) {
            return self::$instance;
        } else {
            throw new CommonException('Must either run Client::configure() first!');
        }
    }

    /**
     * @param  string  $operationId
     * @param  string  $method
     * @param  string  $uri
     * @param  array   $options
     *
     * @return ModelInterface|ModelInterface[]
     */
    public function request(string $operationId, string $method, string $uri, array $options = [])
    {
        if ($this->client instanceof PsrClientInterface) {
            $response = $this->psrRequest($method, $uri, $options);
        } else {
            $response = $this->symfonyRequest($method, $uri, $options);
        }

        return $this->responseHandlerStack->handle($response, $operationId);
    }

    private function psrRequest($method, $uri, array $options = []): PsrResponseInterface
    {
        $options = array_merge($this->defaultOptions, $options);

        $headers         = key_exists(GuzzleHttp\RequestOptions::HEADERS, $options)
            ? $options[GuzzleHttp\RequestOptions::HEADERS] : [];
        $body            = key_exists(GuzzleHttp\RequestOptions::BODY, $options)
            ? $options[GuzzleHttp\RequestOptions::BODY] : null;
        $protocalVersion = key_exists(GuzzleHttp\RequestOptions::VERSION, $options)
            ? $options[GuzzleHttp\RequestOptions::VERSION] : '1.1';

        if (isset($options['json'])) {
            $body = \json_encode($options['json']);
        }

        $request = new Request($method, $uri, $headers, $body, $protocalVersion);

        return $this->client->sendRequest($request);
    }

    private function symfonyRequest($method, $uri, array $options = []): SymfonyResponseInterface
    {
        $options = array_merge($this->defaultOptions, $options);

        return $this->client->request($method, $uri, $options);
    }

    /**
     * @internal Function used for unit test only
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    public function getDefaultOptions(): array
    {
        return $this->defaultOptions;
    }

    public function setDefaultOption($option, $value = null): self
    {
        if (is_array($option) && null === $value) {
            $this->defaultOptions = array_merge($this->defaultOptions, $option);
        } else {
            $this->defaultOptions[$option] = $value;
        }

        return $this;
    }
}