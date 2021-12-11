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
use OpenAPI\Runtime\ResponseHandler\Exception\ResponseHandlerThrowable;
use OpenAPI\Runtime\ResponseHandler\ResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseHandlerStack implements \Iterator, ResponseHandlerStackInterface
{
    /**
     * @var ResponseHandlerInterface[]
     */
    protected array $handlers;

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

    public function handle(ResponseInterface $response, string $operationId): array|ModelInterface|null
    {
        $result = false;

        $this->rewind();
        while ($handler = $this->current()) {
            try {
                $result = $handler($response, $operationId);
                $this->next();
            } catch (ResponseHandlerThrowable $e) {
                if (!$this->next() && false === $result) {
                    throw $e;
                }
            }
        }

        return $result;
    }

    public function current(): bool|ResponseHandlerInterface
    {
        return current($this->handlers);
    }

    public function key(): int
    {
        return key($this->handlers);
    }

    public function next(): bool|ResponseHandlerInterface
    {
        return next($this->handlers);
    }

    public function rewind(): bool|ResponseHandlerInterface
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
