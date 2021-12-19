<?php

namespace OpenAPI\Runtime\ResponseHandler;

use OpenAPI\Runtime\ResponseTypesInterface;

interface ResponseTypesInjecableInterface
{
    /**
     * @param  ResponseTypesInterface  $responseTypes
     */
    public function setResponseTypes(ResponseTypesInterface $responseTypes): void;

    /**
     * @return ResponseTypesInterface
     */
    public function getResponseTypes(): ResponseTypesInterface;
}
