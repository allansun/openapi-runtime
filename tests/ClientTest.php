<?php /** @noinspection PhpUndefinedMethodInspection */

namespace OpenAPI\Runtime\Tests;

use GuzzleHttp;
use OpenAPI\Runtime\Client;
use OpenAPI\Runtime\Exception\CommonException;
use OpenAPI\Runtime\Exception\IncompatibleTransportClientException;
use OpenAPI\Runtime\ResponseHandler\JsonSymfonyResponseHandler;
use OpenAPI\Runtime\ResponseHandlerStack;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\Tests\Fixtures\TestModel;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpClient as SymfonyClient;

class ClientTest extends TestCase
{
    use ProphecyTrait;

    public function testConfigure()
    {
        Client::configure(new SymfonyClient\MockHttpClient());
        $this->assertInstanceOf(Client::class, Client::getInstance());

        Client::configure(null, null, ['foo' => 'bar']);
        $this->assertEquals('bar', Client::getInstance()->getDefaultOptions()['foo']);
    }

    public function testConfigureShouldFailWithIncompatibleTransportClientException()
    {
        $this->expectException(IncompatibleTransportClientException::class);

        // Delirately pass a random thing into it....
        Client::configure(new SymfonyClient\Response\MockResponse());
    }

    /**
     * @depends testConfigure
     */
    public function testGetInstance()
    {
        Client::configure();
        $this->assertInstanceOf(Client::class, Client::getInstance());
    }

    public function testRequest()
    {
        ResponseTypes::setTypes([
            'test' => [
                '200.' => TestModel::class
            ]
        ]);

        $response = new GuzzleHttp\Psr7\Response(200, [], json_encode(['_underscoredName' => 'abc']));

        $client = $this->prophesize(GuzzleHttp\Client::class);
        $client->send(Argument::any(), Argument::any())->willReturn($response);

        Client::configure($client->reveal());

        $instance = Client::getInstance();

        /** @var TestModel $response */
        $response = $instance->request('test', 'GET', 'test', [
            'json' => [
                'test' => 'abc'
            ]
        ]);

        $this->assertInstanceOf(TestModel::class, $response);
        $this->assertEquals('abc', $response->_underscoredName);

        $client->send(Argument::any(), Argument::any())->shouldHaveBeenCalled();
    }

    public function testRequestWithSymfonyClient()
    {
        ResponseTypes::setTypes([
            'test' => [
                '200.' => TestModel::class
            ]
        ]);

        $response = new SymfonyClient\Response\MockResponse(json_encode(['_underscoredName' => 'abc']));

        $client = new SymfonyClient\MockHttpClient($response);

        Client::configure($client);
        $instance = Client::getInstance();
        $instance->setResponseHandlerStack(new ResponseHandlerStack([new JsonSymfonyResponseHandler()]));

        /** @var TestModel $response */
        $response = $instance->request('test', 'GET', 'http://test.com', [
            'json' => [
                'test' => 'abc'
            ]
        ]);

        $this->assertInstanceOf(TestModel::class, $response);
        $this->assertEquals('abc', $response->_underscoredName);
    }

    /**
     * @depends testGetInstance
     */
    public function testSetDefaultOption()
    {
        Client::configure();
        $instance = Client::getInstance();
        $instance->setDefaultOption(['a' => 'a']);
        $instance->setDefaultOption('b', 'b');

        $defaultOptions = $instance->getDefaultOptions();

        $this->assertEquals('a', $defaultOptions['a']);
        $this->assertEquals('b', 'b');
    }


    public function testUninitializedInstance()
    {
        $this->expectException(CommonException::class);
        Client::getInstance();
    }

    protected function setUp(): void
    {
        Client::reset();
    }

}
