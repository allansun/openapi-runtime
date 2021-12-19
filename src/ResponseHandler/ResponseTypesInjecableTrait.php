<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\ResponseTypesInterface;

trait ResponseTypesInjecableTrait
{
    private ?ResponseTypesInterface $responseTypes;

    public function getResponseTypes(): ResponseTypesInterface
    {
        if (!isset($this->responseTypes)) {
            $this->responseTypes = new ResponseTypes();
        }

        return $this->responseTypes;
    }

    public function setResponseTypes(ResponseTypesInterface $responseTypes): void
    {
        $this->responseTypes = $responseTypes;
    }
}
