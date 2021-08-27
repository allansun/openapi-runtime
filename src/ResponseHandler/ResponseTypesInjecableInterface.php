<?php


namespace OpenAPI\Runtime\ResponseHandler;


use OpenAPI\Runtime\ResponseTypesInterface;

interface ResponseTypesInjecableInterface
{
    /**
     * @param  ResponseTypesInterface  $responseTypes
     *
     * @return self
     */
    public function setResponseTypes(ResponseTypesInterface $responseTypes): ResponseTypesInjecableInterface;

    /**
     * @return ResponseTypesInterface
     */
    public function getResponseTypes(): ResponseTypesInterface;
}