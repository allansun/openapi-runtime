<?php

namespace OpenAPI\Runtime\ResponseHandler\Exception;

use OpenAPI\Runtime\AbstractException;

class UndefinedResponseException extends AbstractException implements ResponseHandlerThrowable
{
    /**
     * @param  string      $operationId
     * @param  string|int  $statusCode
     */
    public function __construct(string $operationId, $statusCode)
    {
        parent::__construct(sprintf(
            "Operation '%s' dose not have a defined response when status code is %s",
            $operationId,
            $statusCode
        ));
    }
}
