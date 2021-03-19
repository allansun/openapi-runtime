<?php

namespace OpenAPI\Runtime;

class ResponseTypes
{
    private static array $types = [];

    public static function getTypes(): array
    {
        return self::$types;
    }

    public static function setTypes(array $types): void
    {
        self::$types = $types;
    }


}

