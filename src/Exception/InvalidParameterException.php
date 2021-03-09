<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenApiRuntime\Exception;


class InvalidParameterException extends AbstractException
{
    /**
     * InvalidParameterException constructor.
     *
     * @param  string       $key
     * @param  null|string  $value
     */
    public function __construct(string $key, $value = null)
    {
        parent::__construct(sprintf("Invalid parameter [%s] was given with value [%s]", $key, $value));
    }
}