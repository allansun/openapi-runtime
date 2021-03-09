<?php

namespace OpenApiRuntime;

use OpenApiRuntime\ResponseHandler\JsonPsrResponseHandler;

final class DefaultResponseHandlerStack extends ResponseHandlerStack
{

    public function __construct()
    {
        $this->add(new JsonPsrResponseHandler());
    }
}
