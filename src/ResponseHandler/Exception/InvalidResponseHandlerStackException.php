<?php

namespace OpenAPI\Runtime\ResponseHandler\Exception;

use OpenAPI\Runtime\AbstractException;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStackInterface;

class InvalidResponseHandlerStackException extends AbstractException implements ResponseHandlerThrowable
{
    public function __construct()
    {
        $this->message = sprintf(
            "Your \$responseHandlerStackClass should be compatible with %s.",
            ResponseHandlerStackInterface::class
        );
        parent::__construct();
    }
}
