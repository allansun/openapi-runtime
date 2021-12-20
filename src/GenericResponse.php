<?php

namespace OpenAPI\Runtime;

class GenericResponse extends AbstractModel implements GenericResponseInterface
{
    /**
     * @var int|string
     */
    public string|int $statusCode;

    /**
     * @var string
     */
    public string $contents;

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return (int)$this->statusCode;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }
}
