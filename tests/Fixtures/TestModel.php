<?php


namespace OpenAPI\Runtime\Tests\Fixtures;


use OpenAPI\Runtime\AbstractModel;

class TestModel extends AbstractModel
{
    public $namespace = 'test-namespace';

    /**
     * @var TestRawModel|null
     */
    public $status = null;

    /**
     * @var AnotherTestModel[]
     */
    public $metadata = [];

    /**
     * @var TestRawModel[]
     */
    public $rawModels = [];

    /**
     * @var AnotherTestModel|null
     */
    public $testModel = null;

    /**
     * @var TestObject|null
     */
    public $testObject = null;

    /**
     * @var TestObject[]
     */
    public $testObjects = [];

    /**
     * @var string
     */
    public $_underscoredName = 'test';
}