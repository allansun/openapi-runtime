<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseHandler\JsonSymfonyResponseHandler;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class JsonSymfonyResponseHandlerTest extends TestCase
{
    public function testInvokeShouldFailWithUndefinedResponseException()
    {
        $handler    = new JsonSymfonyResponseHandler();
        $mockClient = new MockHttpClient(new MockResponse('{"foo":"bar"}'));

        $response = $mockClient->request('GET', 'http://test.com');

        $this->expectException(UndefinedResponseException::class);
        $handler($response, 'NOT_EXIST');
    }

    public function testInvokeShouldFailWithUnparsableException()
    {
        $handler    = new JsonSymfonyResponseHandler();
        $mockClient = new MockHttpClient(new MockResponse('not_a_valid_json'));

        $response = $mockClient->request('GET', 'http://test.com');

        $this->expectException(UnparsableException::class);
        $handler($response, 'test');
    }

    public function testInvokeWithArrayResponses()
    {
        ResponseTypes::setTypes([
            'test' => [
                '200.' => TestModel::class . '[]'
            ]
        ]);
        $handler = new JsonSymfonyResponseHandler();
        $mockClient = new MockHttpClient(new MockResponse('[{"namespace":"aaa"},{"namespace":"bbb"}]'));

        $responses = $handler($mockClient->request('GET', 'http://test.com'), 'test');

        $this->assertIsArray($responses);
        foreach ($responses as $response) {
            $this->assertInstanceOf(TestModel::class, $response);
        }
    }

    public function testInvokeWithNormalResponse()
    {
        ResponseTypes::setTypes([
            'test' => [
                '200.' => TestModel::class
            ]
        ]);
        $handler = new JsonSymfonyResponseHandler();
        $mockClient = new MockHttpClient(new MockResponse('{"namespace":"aaa"}'));

        $response = $handler($mockClient->request('GET', 'http://test.com'), 'test');

        $this->assertInstanceOf(TestModel::class, $response);
    }

    public function test__invokeShouldFail()
    {
        $handler = new JsonSymfonyResponseHandler();

        $this->expectException(IncompatibleResponseException::class);
        $handler(new Response(), 'test');
    }

    protected function setUp(): void
    {
        // reset ResponseTypes
        ResponseTypes::setTypes([]);
    }
}
