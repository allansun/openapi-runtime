<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseHandler\JsonResponseHandler;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
use PHPUnit\Framework\TestCase;

class JsonResponseHandlerTest extends TestCase
{
    public function testInvokeShouldFailWithUndefinedResponseException(): void
    {
        $handler = new JsonResponseHandler();

        $this->expectException(UndefinedResponseException::class);
        $handler(new Response(200, [], '{"foo":"bar"}'), 'NOT_EXIST');
    }

    public function testInvokeShouldFailWithUnparsableException(): void
    {
        $handler = new JsonResponseHandler();

        $this->expectException(UnparsableException::class);
        $handler(new Response(200, [], 'not_a_valid_json'), 'test');
    }

    public function testInvokeWithNormalResponse(): void
    {
        $responseTypes = new ResponseTypes();
        $responseTypes->setTypes([
            'test' => [
                '200.' => TestModel::class
            ]
        ]);
        $handler = new JsonResponseHandler();
        $handler->setResponseTypes($responseTypes);

        $response = $handler(new Response(200, [], '{"namespace":"aaa"}'), 'test');

        $this->assertInstanceOf(TestModel::class, $response);
    }

    public function testInvokeWithAbnormalResponseStatusCode(): void
    {
        $this->expectException(UndefinedResponseException::class);
        $this->expectExceptionMessageMatches('/.*401.*/');

        $responseTypes = new ResponseTypes();
        $responseTypes->setTypes([
            'test' => [
                '200.' => TestModel::class
            ]
        ]);
        $handler = new JsonResponseHandler();
        $handler->setResponseTypes($responseTypes);

        $handler(new Response(401, [], '{"namespace":"aaa"}'), 'test');
    }

    public function testInvokeWithArrayResponses(): void
    {
        $responseTypes = new ResponseTypes();
        $responseTypes->setTypes([
            'test' => [
                '200.' => TestModel::class . '[]'
            ]
        ]);
        $handler = new JsonResponseHandler();
        $handler->setResponseTypes($responseTypes);

        $responses = $handler(new Response(200, [], '[{"namespace":"aaa"},{"namespace":"bbb"}]'), 'test');

        $this->assertIsArray($responses);
        foreach ($responses as $response) {
            $this->assertInstanceOf(TestModel::class, $response);
        }
    }

    public function testSetResponseTypes(): void
    {
        $handler = new JsonResponseHandler();
        $handler->setResponseTypes(new ResponseTypes());
        $this->assertInstanceOf(ResponseTypes::class, $handler->getResponseTypes());
    }

}
