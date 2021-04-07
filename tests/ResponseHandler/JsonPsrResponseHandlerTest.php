<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\Exception\IncompatibleResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseHandler\JsonPsrResponseHandler;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
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

    public function testInvokeWithNormalResponse()
    {
        ResponseTypes::setTypes([
            'test' => [
                '200.' => TestModel::class
            ]
        ]);
        $handler = new JsonPsrResponseHandler();

        $response = $handler(new Response(200, [], '{"namespace":"aaa"}'), 'test');

        $this->assertInstanceOf(TestModel::class, $response);
    }

    public function testInvokeWithArrayResponses()
    {
        ResponseTypes::setTypes([
            'test' => [
                '200.' => TestModel::class . '[]'
            ]
        ]);
        $handler = new JsonPsrResponseHandler();

        $responses = $handler(new Response(200, [], '[{"namespace":"aaa"},{"namespace":"bbb"}]'), 'test');

        $this->assertIsArray($responses);
        foreach ($responses as $response) {
            $this->assertInstanceOf(TestModel::class, $response);
        }
    }

    public function test__invokeShouldFail()
    {
        $handler = new JsonPsrResponseHandler();

        $this->expectException(IncompatibleResponseException::class);
        $handler(new MockResponse(), 'test');
    }

    public function testSetResponseTypes(){
        $handler =  JsonPsrResponseHandler::setResponseTypes(new ResponseTypes());
        $this->assertIsCallable($handler);
        $this->assertInstanceOf(ResponseTypes::class,$handler->getResponseTypes());
    }

    protected function setUp(): void
    {
        // reset ResponseTypes
        ResponseTypes::setTypes([]);
    }
}
