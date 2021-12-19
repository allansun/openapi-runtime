<?php

namespace OpenAPI\Runtime\ResponseHandler\Exception;

use OpenAPI\Runtime\AbstractException;

class UndefinedResponseException extends AbstractException implements ResponseHandlerThrowable
{
    public function __construct($operationId, $statusCode)
    {
        parent::__construct(sprintf(
            "Operation '%s' dose not have a defined response when status code is %s",
            $operationId,
            $statusCode
        ));
    }
}
