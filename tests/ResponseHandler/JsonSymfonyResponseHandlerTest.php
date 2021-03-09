<?php

namespace OpenApiRuntime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenApiRuntime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenApiRuntime\ResponseHandler\Exception\UndefinedResponseException;
use OpenApiRuntime\ResponseHandler\Exception\UnparsableException;
use OpenApiRuntime\ResponseHandler\JsonSymfonyResponseHandler;
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
