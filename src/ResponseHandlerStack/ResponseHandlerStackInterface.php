<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime\ResponseHandlerStack;

use OpenAPI\Runtime\ModelInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseHandlerStackInterface
{
    public function handle(ResponseInterface $response, string $operationId): array|ModelInterface|null;
}