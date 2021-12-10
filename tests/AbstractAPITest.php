<?php

namespace OpenAPI\Runtime\Tests;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\JsonResponseHandler;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;
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

    public function testSetResponseHandlerStack()
    {
        $api = new TestAPI();
        $this->assertInstanceOf(ResponseHandlerStack::class, $api::getResponseHandlerStack());
    }

    public function testRequest()
    {
        $model = $this->getTestAPI()->getById(1);
        $this->assertInstanceOf(TestModel::class, $model);
    }

    /**
     * @depends testRequest
     */
    public function testRequestWithSimpleQuery()
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function ($request) {
            /** @var RequestInterface $request */
            return 'a=b' == $request->getUri()->getQuery();
        }))->willReturn(new Response('200', [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', null, ['a' => 'b']);
    }


    /**
     * @depends testRequest
     */
    public function testRequestWithArrayQuery()
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function ($request) {
            /** @var RequestInterface $request */
            return 'a%5B%5D=1&a%5B%5D=2' == $request->getUri()->getQuery();
        }))->willReturn(new Response('200', [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', null, ['a[]' => [1, 2]]);
    }

    public function testRequestWithBody()
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function ($request) {
            /** @var RequestInterface $request */
            return json_encode(['test' => 'a']) == $request->getBody()->getContents();
        }))->willReturn(new Response('200', [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', ['test' => 'a'],);
    }

    public function testRequestWithHeaders()
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::that(function ($request) {
            /** @var RequestInterface $request */
            return ['a'] == $request->getHeader('test');
        }))->willReturn(new Response('200', [], (new TestModel())->toJson()))->shouldBeCalled();

        $api = new TestAPI($client->reveal());
        $api->request('testApiGetById', 'GET', '/test', null, [], ['test' => 'a']);
    }

    private function getTestAPI(): TestAPI
    {
        $client = new MockHttpClient([new MockResponse((new TestModel())->toJson())]);
        $api    = new TestAPI(new Psr18Client($client));

        $responseHandler = new JsonResponseHandler();
        $responseHandler->setResponseTypes(new DummyResponseTypes());
        $api::setResponseHandlerStack(new ResponseHandlerStack([$responseHandler]));

        return $api;
    }
}
