<?php

namespace OpenAPI\Runtime\Tests;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\GenericResponseInterface;
use OpenAPI\Runtime\ResponseHandler\UnexpectedResponseHandler;
use OpenAPI\Runtime\ResponseHandler\Allow404ResponseStatusHandler;
use OpenAPI\Runtime\ResponseHandler\Exception\UndefinedResponseException;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\Tests\Fixtures\ResponseHandler\DummyResponseHandler;
use OpenAPI\Runtime\Tests\Fixtures\ResponseHandler\InvalidResponseHandler;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
use PHPUnit\Framework\TestCase;

class ResponseHandlerStackTest extends TestCase
{
    /**
     * @return ResponseHandlerStack[][]
     *
     * @psalm-return array{default: array{0: ResponseHandlerStack}}
     */
    public function initStack(): array
    {
        $stack = new ResponseHandlerStack();
        $stack->add(new DummyResponseHandler(), 0);

        return ['default' => [$stack]];
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testAdd(ResponseHandlerStack $stack): void
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/.*' . addslashes(DummyResponseHandler::class) . '/');
        $this->assertCount(1, $stack);

        $stack->add(new InvalidResponseHandler(), 0);
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testCurrent(ResponseHandlerStack $stack): void
    {
        $this->assertInstanceOf(DummyResponseHandler::class, $stack->current());
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testHandle(ResponseHandlerStack $stack): void
    {
        $this->assertInstanceOf(TestModel::class, $stack->handle(new Response(), 'test'));
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testMultipleHandler(ResponseHandlerStack $stack): void
    {
        $stack->add(new UnexpectedResponseHandler());
        $stack->add(new Allow404ResponseStatusHandler());

        $this->assertInstanceOf(GenericResponseInterface::class, $stack->handle(new Response(500), 'test'));
        $this->assertEquals(null, $stack->handle(new Response(404), 'test'));
    }

    public function testHandleShouldFail(): void
    {
        $this->expectException(UndefinedResponseException::class);

        $stack = new ResponseHandlerStack();
        $stack->add(new InvalidResponseHandler(), 0);
        $stack->handle(new Response(), 'test');
    }

    public function testKey(): void
    {
        $stack = new ResponseHandlerStack();
        $stack->add(new InvalidResponseHandler(), 100);
        $this->assertEquals(100, $stack->key());
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testRemove(ResponseHandlerStack $stack): void
    {
        $this->assertTrue($stack->remove(DummyResponseHandler::class));
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testRemoveShouldReturnFalse(ResponseHandlerStack $stack): void
    {
        $this->assertFalse($stack->remove(InvalidResponseHandler::class));
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testRewind(ResponseHandlerStack $stack): void
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
    public function testValid(ResponseHandlerStack $stack): void
    {
        $this->assertTrue($stack->valid());
    }
}
