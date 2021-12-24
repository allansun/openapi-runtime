<?php

namespace OpenAPI\Runtime;

class GenericResponse extends AbstractModel implements GenericResponseInterface
{
    /**
     * @var int|string
     */
    public $statusCode;

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
