<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\Tests\Fixtures\AnotherTestModel;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
use OpenAPI\Runtime\Tests\Fixtures\TestObject;
use OpenAPI\Runtime\Tests\Fixtures\TestRawModel;
use PHPUnit\Framework\TestCase;

class AbstractModelTest extends TestCase
{
    public function testExchangeArray(): void
    {
        $TestModel = new AnotherTestModel(['namespace' => 'another']);
        $this->assertEquals('another', $TestModel->namespace);
    }

    public function testExchangeArrayWithString(): void
    {
        $TestModel = new AnotherTestModel(json_encode(['namespace' => 'another']));
        $this->assertEquals('another', $TestModel->namespace);
    }

    public function testGetArrayCopy(): void
    {
        $TestModel = new TestModel([
            'status'           => new TestRawModel(['foo' => 'bar']),
            'testModel'        => new AnotherTestModel(['namespace' => 'test']),
            'metadata'         => [new AnotherTestModel()],
            'testObject'       => new TestObject(),
            'testObjects'      => [new TestObject()],
            '_underscoredName' => 'test'
        ]);
        $data      = $TestModel->getArrayCopy();
        $this->assertArrayHasKey('namespace', $data);
        $this->assertEquals('test-namespace', $data['namespace']);
        $this->assertEquals('test', $data['$underscoredName']);

        $this->assertInstanceOf(TestRawModel::class, $TestModel->status);

        $this->assertIsArray($TestModel->metadata);
        $TestRawModel = $TestModel->metadata[0];
        $this->assertInstanceOf(AnotherTestModel::class, $TestRawModel);
    }

    public function testGetArrayCopyRawObject(): void
    {
        $TestRawModel = new TestRawModel(['foo' => 'bar']);
        $data         = $TestRawModel->getArrayCopy();
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);
    }

    public function testIsRawObject(): void
    {
        $TestRawModel = new TestRawModel();
        $this->assertTrue($TestRawModel->isRawObject());
    }

    public function testToJson(): void
    {
        $TestModel = new TestModel();
        $this->assertJsonStringEqualsJsonString(
            json_encode([
            'namespace'        => 'test-namespace',
            '$underscoredName' => 'test'
            ]),
            $TestModel->toJson()
        );
    }
}
