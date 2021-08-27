<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\ResponseHandler\JsonPsrResponseHandler;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\SimplePsrResponseHandlerStack;
use PHPUnit\Framework\TestCase;

class SimplePsrResponseHandlerStackTest extends TestCase
{

    public function test__construct()
    {
        ResponseTypes::setTypes(['a' => 'b']);

        $stack = new SimplePsrResponseHandlerStack(new ResponseTypes());

        /** @var JsonPsrResponseHandler $handler */
        $handler = $stack->current();

        $this->assertEquals('b', $handler->getResponseTypes()::getTypes()['a']);
    }
}
