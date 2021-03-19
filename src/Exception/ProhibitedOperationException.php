<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime\Exception;


class ProhibitedOperationException extends AbstractException
{


    /**
     * ProhibitedActionException constructor.
     *
     * @param  null|string  $action
     */
    public function __construct($action = null)
    {
        parent::__construct(sprintf("Action [%s] is prohibited for this resource.", $action));
    }
}