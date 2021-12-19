<?php

/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
