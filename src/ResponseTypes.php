<?php

namespace OpenAPI\Runtime;

class ResponseTypes implements ResponseTypesInterface
{
    /**
     * @var array
     */
    protected static array $types = [];

    public static function getTypes(): array
    {
        return static::$types;
    }

    public static function setTypes(array $types): void
    {
        static::$types = $types;
    }
}

