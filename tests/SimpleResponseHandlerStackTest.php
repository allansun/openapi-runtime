<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\ResponseHandler\JsonResponseHandler;
use OpenAPI\Runtime\ResponseHandlerStack\JsonResponseHandlerStack;
use OpenAPI\Runtime\ResponseTypes;
use PHPUnit\Framework\TestCase;

class SimpleResponseHandlerStackTest extends TestCase
{
    public function testConstruct(): void
    {
        $responseTypes = new ResponseTypes();
        $responseTypes->setTypes(['a' => 'b']);

        $stack = new JsonResponseHandlerStack($responseTypes);

        /** @var JsonResponseHandler $handler */
        $handler = $stack->current();

        $this->assertEquals('b', $handler->getResponseTypes()->getTypes()['a']);
    }
}
