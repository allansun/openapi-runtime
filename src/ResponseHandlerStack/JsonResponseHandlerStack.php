<?php

/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime\ResponseHandlerStack;

use OpenAPI\Runtime\ResponseHandler\JsonResponseHandler;
use OpenAPI\Runtime\ResponseTypesInterface;

class JsonResponseHandlerStack extends ResponseHandlerStack
{
    public function __construct(?ResponseTypesInterface $responseTypes = null)
    {
        $handler = new JsonResponseHandler();
        if ($responseTypes) {
            $handler->setResponseTypes($responseTypes);
        }

        parent::__construct([$handler]);
    }
}
