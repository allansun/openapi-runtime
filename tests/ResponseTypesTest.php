<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\Tests\Fixtures\DummyResponseTypes;
use PHPUnit\Framework\TestCase;

class ResponseTypesTest extends TestCase
{
    public function testDummyResponseTypes(): void
    {
        $responseTypes = new DummyResponseTypes();
        $this->assertArrayHasKey('a', $responseTypes->getTypes());
    }

    public function testSetGetTypes(): void
    {
        $responseTypes = new DummyResponseTypes();
        $responseTypes->setTypes(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $responseTypes->getTypes());
    }
}
