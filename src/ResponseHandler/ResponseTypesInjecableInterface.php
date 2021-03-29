<?php


namespace OpenAPI\Runtime\ResponseHandler;


use OpenAPI\Runtime\ResponseTypesInterface;

interface ResponseTypesInjecableInterface
{
    static public function setResponseTypes(ResponseTypesInterface $responseTypes): ResponseHandlerInterface;

    public function getResponseTypes(): ResponseTypesInterface;
}