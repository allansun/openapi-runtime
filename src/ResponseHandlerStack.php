<?php

namespace OpenApiRuntime;

use OpenApiRuntime\ResponseHandler\Exception\UnparsableException;
use OpenApiRuntime\ResponseHandler\ResponseHandlerInterface;

class ResponseHandlerStack implements \Iterator, ResponseHandlerStackInterface
{
    /**
     * @var ResponseHandlerInterface[]
     */
    private iterable $stack = [];

    /**
     * ResponseHandlerStack constructor.
     *
     * @param  iterable|ResponseHandlerInterface[]  $stack
     */
    public function __construct($stack = [])
    {
        $this->stack = $stack;
    }


    public function add(ResponseHandlerInterface $callable, ?int $priority = null): void
    {
        if (null !== $priority && key_exists($priority, $this->stack)) {
            throw new \InvalidArgumentException(sprintf(
                'Priority of %s has already been used by handler %s',
                $priority,
                get_class($this->stack[$priority])
            ));
        }
        $priority = $priority ?? count($this->stack);

        $this->stack[$priority] = $callable;
    }

    public function handle($response, string $operationId): ModelInterface
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
        return current($this->stack);
    }

    public function key()
    {
        return key($this->stack);
    }

    public function next(): bool
    {
        return next($this->stack);
    }

    /**
     * @return ResponseHandlerInterface|false
     */
    public function rewind()
    {
        return reset($this->stack);
    }

    public function valid(): bool
    {
        $key = key($this->stack);

        return ($key !== null && $key !== false);
    }

    public function remove(string $handlerClass): bool
    {
        while ($handler = $this->current()) {
            if ($handlerClass === get_class($handler)) {
                unset($this->stack[$this->key()]);

                return true;
            }
            $this->next();
        }

        return false;
    }
}
