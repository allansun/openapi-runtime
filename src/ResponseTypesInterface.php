<?php


namespace OpenAPI\Runtime;


interface ResponseTypesInterface
{
    public static function getTypes(): array;

    public static function setTypes(array $types): void;
}