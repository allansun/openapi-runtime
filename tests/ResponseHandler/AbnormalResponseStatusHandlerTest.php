<?php

namespace OpenAPI\Runtime\Tests\ResponseHandler;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\AbnormalHttpStatus;
use OpenAPI\Runtime\ResponseHandler\AbnormalResponseStatusHandler;
use OpenAPI\Runtime\ResponseHandler\Exception\ResponseHandlerThrowable;
use PHPUnit\Framework\TestCase;

class AbnormalResponseStatusHandlerTest extends TestCase
{
    public function test__invoke(): void
    {
        $handler = new AbnormalResponseStatusHandler();

        $response = $handler(new Response(500, [], '{"namespace":"aaa"}'), 'test');

        $this->assertInstanceOf(AbnormalHttpStatus::class, $response);
        $this->assertEquals(500, $response->statusCode);
        $this->assertStringContainsStringIgnoringCase('namespace', $response->contents);
    }

    public function test__invokeShouldThrowException(): void
    {
        $this->expectException(ResponseHandlerThrowable::class);

        $handler = new AbnormalResponseStatusHandler();

        $handler(new Response(200, [], '{"namespace":"aaa"}'), 'test');
    }
}
