<?php

namespace OpenAPI\Runtime;

use Psr\Http\Client\ClientInterface;

interface APIInterface
{
    public function __construct(ClientInterface $client);
}
