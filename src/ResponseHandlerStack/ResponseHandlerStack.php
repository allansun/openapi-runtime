<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime\ResponseHandlerStack;

use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandler\Exception\UnparsableException;
use OpenAPI\Runtime\ResponseHandler\ResponseHandlerInterface;

class ResponseHandlerStack implements \Iterator, ResponseHandlerStackInterface
{
    /**
     * @var ResponseHandlerInterface[]
     */
    private $handlers;

    /**
     * ResponseHandlerStack constructor.
     *
     * @param  ResponseHandlerInterface[]  $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }


    public function add(ResponseHandlerInterface $callable, ?int $priority = null): void
    {
        if (null !== $priority && key_exists($priority, $this->handlers)) {
            throw new \InvalidArgumentException(sprintf(
                'Priority of %s has already been used by handler %s',
                $priority,
                get_class($this->handlers[$priority])
            ));
        }
        $priority = $priority ?? count($this->handlers);

        $this->handlers[$priority] = $callable;
    }

    /**
     * @param          $response
     * @param  string  $operationId
     *
     * @return ModelInterface|ModelInterface[]|null
     * @throws UnparsableException
     */
    public function handle($response, string $operationId)
    {
        $result = null;

        while ($handler = $this->current()) {
            try {
                $result = $handler($response, $operationId);
            } catch (UnparsableException $e) {
                if (!$this->next()) {
                    throw $e;
                }
            }
            $this->next();
        }

        return $result;
    }

    /**
     * @return ResponseHandlerInterface|false
     */
    public function current()
    {
        return current($this->handlers);
    }

    public function key()
    {
        return key($this->handlers);
    }

    public function next(): bool
    {
        return next($this->handlers);
    }

    /**
     * @return ResponseHandlerInterface|false
     */
    public function rewind()
    {
        return reset($this->handlers);
    }

    public function valid(): bool
    {
        $key = key($this->handlers);

        return $key !== null;
    }

    public function remove(string $handlerClass): bool
    {
        while ($handler = $this->current()) {
            if ($handlerClass === get_class($handler)) {
                unset($this->handlers[$this->key()]);

                return true;
            }
            $this->next();
        }

        return false;
    }
}
