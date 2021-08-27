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

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Psr7\Request;
use OpenAPI\Runtime\Exception\CommonException;
use OpenAPI\Runtime\Exception\IncompatibleTransportClientException;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as SymfonyResponseInterface;

class Client implements OpenApiClientInterface
{
    public const HEADERS = 'headers';
    public const BODY = 'body';
    public const PROTOCAL_VERSION = 'version';
    public const JSON = 'json';

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
        $client,
        ?ResponseHandlerStackInterface $responseHandlerStack = null,
        array $defaultOptions = []
    ) {
        $this->defaultOptions = $defaultOptions;

        if (!($client instanceof SymfonyHttpClientInterface) && !($client instanceof PsrClientInterface)) {
            throw new IncompatibleTransportClientException();
        }
        $this->client = $client;

        $this->setResponseHandlerStack(
            $this->responseHandlerStack ??
            $responseHandlerStack ??
            new SimplePsrResponseHandlerStack()
        );

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

        $headers         = key_exists(static::HEADERS, $options) ? $options[static::HEADERS] : [];
        $body            = key_exists(static::BODY, $options) ? $options[static::BODY] : null;
        $protocalVersion = key_exists(static::PROTOCAL_VERSION, $options) ? $options[static::BODY] : '1.1';

        if (isset($options[static::JSON])) {
            $body = \json_encode($options[static::JSON]);
        }

        $request = new Request($method, $uri, $headers, $body, $protocalVersion);

        if ($this->client instanceof GuzzleClientInterface) {
            return $this->client->send($request, $options);
        } else {
            return $this->client->sendRequest($request);
        }
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