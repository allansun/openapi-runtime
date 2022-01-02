<?php

namespace OpenAPI\Runtime\Tests;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\Exception\InvalidResponseHandlerStackException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\JsonResponseHandler;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStackInterface;
use OpenAPI\Runtime\Tests\Fixtures\DummyResponseTypes;
use OpenAPI\Runtime\Tests\Fixtures\TestAPI;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\MockResponse;

class AbstractAPITest extends TestCase
{
    use ProphecyTrait;

    public function testSetResponseHandlerStack(): void
    {
        $api = new TestAPI();
        $this->assertInstanceOf(ResponseHandlerStackInterface::class, $api->getResponseHandlerStack());
    }

    public function testRequest(): void
    {
        $model = $this->getTestAPI()->getById(1);
        $this->assertInstanceOf(TestModel::class, $model);
    }

    public function testRequestWithSimpleQuery(): void
    {
        $this->expectException(UndefinedResponseException::class);
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function (RequestInterface $request) {
            return 'a=b' == $request->getUri()->getQuery();
        }))->willReturn(new Response(200, [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', null, ['a' => 'b']);
    }


    public function testRequestWithArrayQuery(): void
    {
        $this->expectException(UndefinedResponseException::class);
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function (RequestInterface $request) {
            return 'a%5B%5D=1&a%5B%5D=2' == $request->getUri()->getQuery();
        }))->willReturn(new Response(200, [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', null, ['a[]' => [1, 2]]);
    }

    public function testRequestWithBody(): void
    {
        $this->expectException(UndefinedResponseException::class);
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function (RequestInterface $request) {
            return json_encode(['test' => 'a']) == $request->getBody()->getContents();
        }))->willReturn(new Response(200, [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', ['test' => 'a']);
    }

    public function testRequestWithHeaders(): void
    {
        $this->expectException(UndefinedResponseException::class);
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function (RequestInterface $request) {
            return ['a'] == $request->getHeader('test');
        }))->willReturn(new Response(200, [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', null, [], ['test' => 'a']);
    }

    public function testWithInvalidResponseHanlderStackClass(): void
    {
        $this->expectException(InvalidResponseHandlerStackException::class);
        $this->expectExceptionMessageMatches('/should be compatible with/');
        $className = uniqid('TestAPI', false);
        eval('namespace OpenAPI\Runtime; class ' . $className . ' extends AbstractAPI {' .
             ' protected string $responseHandlerStackClass = "abc"; }');

        $className = 'OpenAPI\\Runtime\\' . $className;
        new $className();
    }

    private function getTestAPI(): TestAPI
    {
        $client = new MockHttpClient([new MockResponse((new TestModel())->toJson())]);
        $api    = new TestAPI(new Psr18Client($client));

        $responseHandler = new JsonResponseHandler();
        $responseHandler->setResponseTypes(new DummyResponseTypes());
        $api->setResponseHandlerStack(new ResponseHandlerStack([$responseHandler]));

        return $api;
    }
}
