<?php


namespace OpenApiRuntime\Tests\Fixtures;


use OpenApiRuntime\AbstractModel;

class TestModel extends AbstractModel
{
    public string $namespace = 'test-namespace';

    /**
     * @var TestRawModel|null
     */
    public ?TestRawModel $status = null;

    /**
     * @var AnotherTestModel[]
     */
    public array $metadata = [];

    /**
     * @var TestRawModel[]
     */
    public array $rawModels = [];

    /**
     * @var AnotherTestModel|null
     */
    public ?AnotherTestModel $testModel = null;

    /**
     * @var TestObject|null
     */
    public ?TestObject $testObject = null;

    /**
     * @var TestObject[]
     */
    public array $testObjects = [];

    /**
     * @var string
     */
    public string $_underscoredName = 'test';
}