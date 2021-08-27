<?php

namespace OpenAPI\Runtime;

use OpenAPI\Runtime\ResponseHandler\JsonPsrResponseHandler;

final class SimplePsrResponseHandlerStack extends ResponseHandlerStack
{

    public function __construct(?ResponseTypesInterface $responseTypes = null)
    {
        $handler = new JsonPsrResponseHandler();
        if ($responseTypes) {
            $handler->setResponseTypes($responseTypes);
        }

        parent::__construct([$handler]);
    }
}
