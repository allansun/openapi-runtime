<?php

namespace OpenAPI\Runtime;

interface GenericResponseInterface extends ModelInterface
{
    public function getStatusCode(): int;

    public function getContents(): string;
}
