<?php

namespace OpenAPI\Runtime\Tests;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Runtime\AbnormalHttpStatusModelInterface;
use OpenAPI\Runtime\ResponseHandler\AbnormalResponseStatusHandler;
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
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
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
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testCurrent(ResponseHandlerStack $stack)
    {
        $this->assertInstanceOf(DummyResponseHandler::class, $stack->current());
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
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

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testMultipleHandler(ResponseHandlerStack $stack)
    {
        $stack->add(new AbnormalResponseStatusHandler());
        $stack->add(new Allow404ResponseStatusHandler());
        ResponseTypes::setTypes([
            'test' => [
                '200.' => '{success:false}'
            ]
        ]);

        $this->assertInstanceOf(AbnormalHttpStatusModelInterface::class, $stack->handle(new Response(500), 'test'));
        $this->assertEquals(null, $stack->handle(new Response(404), 'test'));
    }

    public function testHandleShouldFail()
    {
        $this->expectException(UndefinedResponseException::class);

        $stack = new ResponseHandlerStack();
        $stack->add(new InvalidResponseHandler(), 0);
        $stack->handle(new Response(), 'test');
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
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testRemove(ResponseHandlerStack $stack)
    {
        $this->assertTrue($stack->remove(DummyResponseHandler::class));
        $this->assertFalse($stack->next());
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
     */
    public function testRemoveShouldReturnFalse(ResponseHandlerStack $stack)
    {
        $this->assertFalse($stack->remove(InvalidResponseHandler::class));
    }

    /**
     * @dataProvider initStack
     *
     * @param  \OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack  $stack
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
