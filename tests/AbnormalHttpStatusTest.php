<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\AbnormalHttpStatus;
use OpenAPI\Runtime\AbnormalHttpStatusModelInterface;
use PHPUnit\Framework\TestCase;

class AbnormalHttpStatusTest extends TestCase
{
    public function test__construct()
    {
        $instance = new AbnormalHttpStatus(['statusCode' => '200', 'contents' => '{"success":true}']);

        $this->assertInstanceOf(AbnormalHttpStatusModelInterface::class, $instance);
        $this->assertEquals(200, $instance->getStatusCode());
        $this->assertStringContainsStringIgnoringCase('success', $instance->getContents());
    }
}
