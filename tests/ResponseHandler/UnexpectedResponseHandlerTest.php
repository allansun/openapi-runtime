<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\UnexpectedResponseHandler;
use OpenAPI\Runtime\UnexpectedResponseInterface;
use PHPUnit\Framework\TestCase;

class UnexpectedResponseHandlerTest extends TestCase
{
    public function testNormal(): void
    {
        $handler  = new UnexpectedResponseHandler();
        $response = new Response(200, [], 'test');
        $result   = $handler($response, 'test');

        $this->assertInstanceOf(UnexpectedResponseInterface::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('test', $result->getContents());
    }
}
