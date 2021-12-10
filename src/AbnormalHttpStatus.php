<?php

namespace OpenAPI\Runtime;

class AbnormalHttpStatus extends AbstractModel implements AbnormalHttpStatusModelInterface
{
    /**
     * @var int|string
     */
    public $statusCode;

    /**
     * @var string
     */
    public $contents;

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
