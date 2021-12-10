<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\ResponseTypesInterface;

trait ResponseTypesInjecableTrait
{
    /**
     * @var ResponseTypesInterface
     */
    private $responseTypes;

    public function getResponseTypes(): ResponseTypesInterface
    {
        if (empty($this->responseTypes)) {
            $this->responseTypes = new ResponseTypes();
        }

        return $this->responseTypes;
    }

    public function setResponseTypes(ResponseTypesInterface $responseTypes): void
    {
        $this->responseTypes = $responseTypes;
    }
}