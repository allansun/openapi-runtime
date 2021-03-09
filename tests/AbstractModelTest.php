<?php

namespace OpenApiRuntime\Tests;

use OpenApiRuntime\Tests\Fixtures\AnotherTestModel;
use OpenApiRuntime\Tests\Fixtures\TestModel;
use OpenApiRuntime\Tests\Fixtures\TestObject;
use OpenApiRuntime\Tests\Fixtures\TestRawModel;
use PHPUnit\Framework\TestCase;

class AbstractModelTest extends TestCase
{

    public function testExchangeArray()
    {
        $TestModel = new AnotherTestModel(['namespace' => 'another']);
        $this->assertEquals('another', $TestModel->namespace);
    }

    public function testExchangeArrayWithString()
    {
        $TestModel = new AnotherTestModel(json_encode(['namespace' => 'another']));
        $this->assertEquals('another', $TestModel->namespace);
    }

    public function testGetArrayCopy()
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

    public function testGetArrayCopyRawObject()
    {
        $TestRawModel = new TestRawModel(['foo' => 'bar']);
        $data         = $TestRawModel->getArrayCopy();
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);

    }

    public function testIsRawObject()
    {
        $TestRawModel = new TestRawModel();
        $this->assertTrue($TestRawModel->isRawObject());
    }

    public function testToJson()
    {
        $TestModel = new TestModel();
        $this->assertJsonStringEqualsJsonString(json_encode([
            'namespace'        => 'test-namespace',
            '$underscoredName' => 'test'
        ]),
            $TestModel->toJson());
    }
}
