<?php


namespace OpenAPI\Runtime\ResponseHandler;


use OpenAPI\Runtime\ResponseTypesInterface;

interface ResponseTypesInjecableInterface
{
    static public function setResponseTypes(ResponseTypesInterface $responseTypes);

    public function getResponseTypes(): ResponseTypesInterface;
}