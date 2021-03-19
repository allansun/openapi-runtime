<?php

namespace OpenAPI\Runtime;

use OpenAPI\Runtime\ResponseHandler\JsonPsrResponseHandler;

final class DefaultResponseHandlerStack extends ResponseHandlerStack
{

    public function __construct()
    {
        parent::__construct([]);
        $this->add(new JsonPsrResponseHandler());
    }
}
