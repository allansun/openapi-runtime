<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\ResponseHandler\Allow404ResponseStatusHandler;
use OpenAPI\Runtime\ResponseHandler\Exception\ResponseHandlerThrowable;
use PHPUnit\Framework\TestCase;

class Allow404ResponseStatusHandlerTest extends TestCase
{
    public function test__invoke()
    {
        $handler = new Allow404ResponseStatusHandler();

        $response = $handler(new Response(404, [], '{"namespace":"aaa"}'), 'test');

        $this->assertEquals(null, $response);
    }

    public function test__invokeShouldThrowException()
    {
        $this->expectException(ResponseHandlerThrowable::class);

        $handler = new Allow404ResponseStatusHandler();

        $handler(new Response(200, [], '{"namespace":"aaa"}'), 'test');
    }
}
