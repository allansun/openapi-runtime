<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\GenericResponse;
use OpenAPI\Runtime\GenericResponseInterface;
use PHPUnit\Framework\TestCase;

class AbnormalHttpStatusTest extends TestCase
{
    public function test__construct(): void
    {
        $instance = new GenericResponse(['statusCode' => '200', 'contents' => '{"success":true}']);

        $this->assertInstanceOf(GenericResponseInterface::class, $instance);
        $this->assertEquals(200, $instance->getStatusCode());
        $this->assertStringContainsStringIgnoringCase('success', $instance->getContents());
    }
}
