<?php

namespace OpenAPI\Runtime\Tests\Fixtures;

use OpenAPI\Runtime\AbstractAPI;
use OpenAPI\Runtime\ModelInterface;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;

class TestAPI extends AbstractAPI
{
    protected string $responseHandlerStackClass = ResponseHandlerStack::class;

    /**
     * @param  int  $id
     *
     * @return array|ModelInterface|null
     */
    public function getById(int $id)
    {
        return $this->request('testApiGetById', 'GET', '/test');
    }
}
