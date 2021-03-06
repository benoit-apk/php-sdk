<?php
namespace ShoppingFeed\Sdk\Test\Hal;

use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ShoppingFeed\Sdk\Hal\HalClient;
use ShoppingFeed\Sdk\Hal\HalLink;
use ShoppingFeed\Sdk\Hal\HalResource;

class HalLinkTest extends TestCase
{
    public function testSetters()
    {
        $client   = $this->createMock(HalClient::class);
        $instance = new HalLink(
            $client,
            'http://base.url',
            [
                'templated' => true,
                'type'      => 'Type',
                'name'      => 'Name',
                'title'     => 'Title',
            ]
        );

        $this->assertEquals('Type', $instance->getType());
        $this->assertEquals('Name', $instance->getName());
        $this->assertEquals('Title', $instance->getTitle());
        $this->assertEquals('http://base.url', $instance->getHref());
        $this->assertTrue($instance->isTemplated());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testHttpClientCalls()
    {
        $client = $this->createMock(HalClient::class);
        $client
            ->expects($this->exactly(5))
            ->method('send')
            ->willReturn(
                $this->createMock(HalResource::class)
            );

        $instance = $this
            ->getMockBuilder(HalLink::class)
            ->setConstructorArgs([$client, 'http://base.url'])
            ->setMethods(['createRequest'])
            ->getMock();

        $instance
            ->expects($this->exactly(5))
            ->method('createRequest')
            ->willReturn(
                $this->createMock(RequestInterface::class)
            );

        $instance->get();
        $instance->delete([]);
        $instance->put([]);
        $instance->patch([]);
        $instance->post([]);
    }

    public function testSend()
    {
        $client = $this->createMock(HalClient::class);
        $client
            ->expects($this->once())
            ->method('send')
            ->willReturn(
                $this->createMock(HalResource::class)
            );

        $instance = $this
            ->getMockBuilder(HalLink::class)
            ->setConstructorArgs([$client, 'http://base.url'])
            ->setMethods(['createRequest'])
            ->getMock();

        $instance->send(
            $this->createMock(RequestInterface::class)
        );
    }

    public function testGetCreateRequest()
    {
        $client = $this->createMock(HalClient::class);
        $client
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn(
                $this->createMock(RequestInterface::class)
            );

        $instance = $this
            ->getMockBuilder(HalLink::class)
            ->setConstructorArgs([$client, 'http://base.url'])
            ->setMethods(['getUri'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('getUri')
            ->willReturn('/fake/uri');

        $instance->createRequest('GET');
    }

    public function testCreateRequestWithContent()
    {
        $client = $this->createMock(HalClient::class);
        $client
            ->expects($this->exactly(3))
            ->method('createRequest')
            ->willReturn(
                $this->createMock(RequestInterface::class)
            );

        $instance = $this
            ->getMockBuilder(HalLink::class)
            ->setConstructorArgs([$client, 'http://base.url'])
            ->setMethods(['getUri'])
            ->getMock();

        $instance
            ->expects($this->exactly(3))
            ->method('getUri')
            ->willReturn('/fake/uri');

        $instance->createRequest('POST');
        $instance->createRequest('PUT');
        $instance->createRequest('PATCH');
    }

    public function testBatchSend()
    {
        $request = ['request'];
        $client  = $this->createMock(HalClient::class);
        $client
            ->expects($this->once())
            ->method('batchSend')
            ->with(
                $this->callback(
                    function ($requests) use ($request) {
                        return $requests === $request;
                    }
                )
            );

        $instance = new HalLink($client, 'http://base.url');

        $instance->batchSend($request);
    }

    public function testBatchSendWithOption()
    {
        $request = ['request'];
        $options = ['test' => 'option'];
        $client  = $this->createMock(HalClient::class);
        $client
            ->expects($this->once())
            ->method('batchSend')
            ->with(
                $this->callback(
                    function ($requests) use ($request) {
                        return $requests === $request;
                    }
                ),
                $this->callback(
                    function ($config) use ($options) {
                        return $config['options'] === $options;
                    }
                )
            );

        $instance = new HalLink($client, 'http://base.url');

        $instance->batchSend($request, null, null, $options);
    }

    public function testBatchSendWithSuccessCallbacks()
    {
        $test    = $this;
        $request = ['request'];
        $success = function () {
            echo 'Success';
        };
        $client  = $this->createMock(HalClient::class);
        $client
            ->expects($this->once())
            ->method('batchSend')
            ->with(
                $this->callback(
                    function ($requests) use ($request) {
                        return $requests === $request;
                    }
                ),
                $this->callback(
                    function ($config) use ($test) {
                        $this->expectOutputString('Success');
                        $config['fulfilled']($test->createMock(ResponseInterface::class));
                        return true;
                    }
                )
            );

        $instance = new HalLink($client, 'http://base.url');

        $instance->batchSend($request, $success, null);
    }

    public function testBatchSendWithErrorCallback()
    {
        $test    = $this;
        $request = ['request'];
        $error   = function () {
            echo 'Error';
        };
        $client  = $this->createMock(HalClient::class);
        $client
            ->expects($this->once())
            ->method('batchSend')
            ->with(
                $this->callback(
                    function ($requests) use ($request) {
                        return $requests === $request;
                    }
                ),
                $this->callback(
                    function ($config) use ($test) {
                        $exception = $test->createMock(RequestException::class);
                        $exception
                            ->expects($this->once())
                            ->method('hasResponse')
                            ->willReturn(true);
                        $exception
                            ->expects($this->once())
                            ->method('getResponse')
                            ->willReturn($this->createMock(ResponseInterface::class));

                        $config['rejected']($exception);
                        $this->expectOutputString('Error');
                        return true;
                    }
                )
            );

        $instance = new HalLink($client, 'http://base.url');

        $instance->batchSend($request, null, $error);
    }

    public function testGetUriDefault()
    {
        $client   = $this->createMock(HalClient::class);
        $instance = $this
            ->getMockBuilder(HalLink::class)
            ->setConstructorArgs([$client, 'http://base.url'])
            ->setMethods(['getHref'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('getHref')
            ->willReturn('');

        $instance->getUri([]);
    }

    public function testGetUriTemplated()
    {
        $client   = $this->createMock(HalClient::class);
        $instance = new HalLink($client, 'http://base.url', ['templated' => true]);

        $this->assertInternalType('string', $instance->getUri([]));
    }
}
