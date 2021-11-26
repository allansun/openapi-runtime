<?php

namespace OpenAPI\Runtime;

interface APIInterface
{
    public function __construct(?OpenApiClientInterface $client = null);
}
