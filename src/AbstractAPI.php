<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime;


abstract class AbstractAPI
{
    /**
     * @var OpenApiClientInterface
     */
    protected $client;

    public function __construct(?OpenApiClientInterface $client = null)
    {
        if (null == $client) {
            $this->client = Client::getInstance();
        } else {
            $this->client = $client;
        }
    }
}