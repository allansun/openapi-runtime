<?php

namespace OpenAPI\Runtime;

interface AbnormalHttpStatusModelInterface extends ModelInterface
{
    public function getStatusCode(): int;

    public function getContents(): string;
}
