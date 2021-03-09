<?php

namespace OpenApiRuntime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenApiRuntime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenApiRuntime\ResponseHandler\Exception\UndefinedResponseException;
use OpenApiRuntime\ResponseHandler\Exception\UnparsableException;
use OpenApiRuntime\ResponseHandler\JsonPsrResponseHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class JsonPsrResponseHandlerTest extends TestCase
{

    public function testInvokeShouldFailWithUndefinedResponseException()
    {
        $handler = new JsonPsrResponseHandler();

        $this->expectException(UndefinedResponseException::class);
        $handler(new Response(200, [], '{"foo":"bar"}'), 'NOT_EXIST');
    }

    public function testInvokeShouldFailWithUnparsableException()
    {
        $handler = new JsonPsrResponseHandler();

        $this->expectException(UnparsableException::class);
        $handler(new Response(200, [], 'not_a_valid_json'), 'test');
    }

    public function test__invokeShouldFail()
    {
        $handler = new JsonPsrResponseHandler();

        $this->expectException(IncompatibleResponseException::class);
        $handler(new MockResponse(), 'test');
    }
}
