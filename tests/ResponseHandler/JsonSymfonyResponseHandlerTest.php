<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseHandler\JsonSymfonyResponseHandler;
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

    public function test__invokeShouldFail()
    {
        $handler = new JsonSymfonyResponseHandler();

        $this->expectException(IncompatibleResponseException::class);
        $handler(new Response(), 'test');
    }
}
