<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\Tests\Fixtures\DummyResponseTypes;
use PHPUnit\Framework\TestCase;

class ResponseTypesTest extends TestCase
{

    public function testDummyResponseTypes()
    {
        $this->assertArrayHasKey('a', DummyResponseTypes::getTypes());
    }

    public function testSetGetTypes()
    {
        ResponseTypes::setTypes(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], ResponseTypes::getTypes());
    }
}
