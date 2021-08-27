<?php

namespace OpenAPI\Runtime\Tests;

use OpenAPI\Runtime\ResponseHandler\JsonSymfonyResponseHandler;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\SimpleSymfonyResponseHandlerStack;
use PHPUnit\Framework\TestCase;

class SimpleSymfonyResponseHandlerStackTest extends TestCase
{

    public function test__construct()
    {
        ResponseTypes::setTypes(['a' => 'b']);

        $stack = new SimpleSymfonyResponseHandlerStack(new ResponseTypes());

        /** @var JsonSymfonyResponseHandler $handler */
        $handler = $stack->current();

        $this->assertEquals('b', $handler->getResponseTypes()::getTypes()['a']);
    }
}
