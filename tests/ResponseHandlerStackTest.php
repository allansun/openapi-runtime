<?php

namespace OpenApiRuntime\Tests;

use GuzzleHttp\Psr7\Response;
use OpenApiRuntime\ResponseHandler\Exception\UndefinedResponseException;
use OpenApiRuntime\ResponseHandlerStack;
use OpenApiRuntime\ResponseTypes;
use OpenApiRuntime\Tests\Fixtures\ResponseHandler\DummyResponseHandler;
use OpenApiRuntime\Tests\Fixtures\ResponseHandler\InvalidResponseHandler;
use OpenApiRuntime\Tests\Fixtures\TestModel;
use PHPUnit\Framework\TestCase;

class ResponseHandlerStackTest extends TestCase
{
    public function initStack(): array
    {
        $stack = new ResponseHandlerStack();
        $stack->add(new DummyResponseHandler(), 0);

        return ['default' => [$stack]];
    }

    public function setUp(): void
    {
        ResponseTypes::setTypes([]);
    }

    /**
     * @dataProvider initStack
     *
     * @param  ResponseHandlerStack  $stack
     */
    public function testAdd(ResponseHandlerStack $stack)
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/.*' . addslashes(DummyResponseHandler::class) . '/');
        $this->assertCount(1, $stack);

        $stack->add(new InvalidResponseHandler(), 0);
    }

    /**
     * @dataProvider initStack
     *
     * @param  ResponseHandlerStack  $stack
     */
    public function testCurrent(ResponseHandlerStack $stack)
    {
        $this->assertInstanceOf(DummyResponseHandler::class, $stack->current());
    }

    /**
     * @dataProvider initStack
     *
     * @param  ResponseHandlerStack  $stack
     */
    public function testHandle(ResponseHandlerStack $stack)
    {
        ResponseTypes::setTypes([
            'test' => [
                '200.' => TestModel::class
            ]
        ]);

        $this->assertInstanceOf(TestModel::class, $stack->handle(new Response(), 'test'));
    }

    public function testHandleShouldFail()
    {
        $stack = new ResponseHandlerStack();
        $stack->add(new InvalidResponseHandler(), 0);
        $this->expectException(UndefinedResponseException::class);
        $this->assertInstanceOf(TestModel::class, $stack->handle(new Response(), 'test'));
    }

    public function testKey()
    {
        $stack = new ResponseHandlerStack();
        $stack->add(new InvalidResponseHandler(), 100);
        $this->assertEquals(100, $stack->key());
    }

    public function testNext()
    {
        $stack = new ResponseHandlerStack();
        $this->assertFalse($stack->next());
    }

    /**
     * @dataProvider initStack
     *
     * @param  ResponseHandlerStack  $stack
     */
    public function testRemove(ResponseHandlerStack $stack)
    {
        $this->assertTrue($stack->remove(DummyResponseHandler::class));
        $this->assertFalse($stack->next());
    }

    /**
     * @dataProvider initStack
     *
     * @param  ResponseHandlerStack  $stack
     */
    public function testRemoveShouldReturnFalse(ResponseHandlerStack $stack)
    {
        $this->assertFalse($stack->remove(InvalidResponseHandler::class));
    }

    /**
     * @dataProvider initStack
     *
     * @param  ResponseHandlerStack  $stack
     */
    public function testRewind(ResponseHandlerStack $stack)
    {
        $stack->next();
        $stack->rewind();
        $this->assertInstanceOf(DummyResponseHandler::class, $stack->current());
    }

    /**
     * @dataProvider initStack
     *
     * @param  ResponseHandlerStack  $stack
     */
    public function testValid(ResponseHandlerStack $stack)
    {
        $this->assertTrue($stack->valid());
    }
}
