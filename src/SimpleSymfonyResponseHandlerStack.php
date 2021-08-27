<?php

namespace OpenAPI\Runtime;

use OpenAPI\Runtime\ResponseHandler\JsonSymfonyResponseHandler;

final class SimpleSymfonyResponseHandlerStack extends ResponseHandlerStack
{

    public function __construct(?ResponseTypesInterface $responseTypes = null)
    {
        $handler = new JsonSymfonyResponseHandler();
        if ($responseTypes) {
            $handler->setResponseTypes($responseTypes);
        }

        parent::__construct([$handler]);
    }
}
