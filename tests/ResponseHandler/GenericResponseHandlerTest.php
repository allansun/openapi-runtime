<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\GenericResponseInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandler\GenericResponseHandler;
use OpenAPI\Runtime\ResponseTypes;
use PHPUnit\Framework\TestCase;

class GenericResponseHandlerTest extends TestCase
{
    public function testNormal(): void
    {
        $handler       = new GenericResponseHandler();
        $responseTypes = new ResponseTypes();
        $responseTypes->setTypes([
            'test' => [
                '200.' => 'OpenAPI\\Runtime\\GenericResponse'
            ]
        ]);
        $handler->setResponseTypes($responseTypes);

        $response = new Response(200, [], 'test');
        $result   = $handler($response, 'test');

        $this->assertInstanceOf(GenericResponseInterface::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('test', $result->getContents());
    }

    public function testExceptionThrown(): void
    {
        $handler       = new GenericResponseHandler();
        $responseTypes = new ResponseTypes();
        $responseTypes->setTypes([
            'test' => [
                '200.' => 'ok'
            ]
        ]);
        $handler->setResponseTypes($responseTypes);

        $this->expectException(UndefinedResponseException::class);

        $response = new Response(404);
        $handler($response, 'test');
    }
}
