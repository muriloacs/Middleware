<?php

namespace MiddlewareTest\Service\Factory;

use Middleware\Service\Factory\MiddlewareServiceFactory;
use Middleware\Service\MiddlewareService;
use Zend\ServiceManager\ServiceManager;
use Zend\Http\PhpEnvironment\Request;

class MiddlewareServiceFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MiddlewareServiceFactory
     */
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new MiddlewareServiceFactory();
    }

    public function testFactoryShouldCreateMiddlewareService()
    {
        $serviceManager = $this->createServiceManagerMock();
        $actual = $this->factory->createService($serviceManager);
        $this->assertInstanceOf(MiddlewareService::class, $actual);
    }

    /**
     * @return ServiceManager|\PHPUnit_Framework_MockObject_MockObject|
     */
    private function createServiceManagerMock()
    {
        $serviceManager = $this->getMock(ServiceManager::class, ['get']);
        $serviceManager->expects($this->at(0))->method('get')->willReturn($this->createRequestMock());
        $serviceManager->expects($this->at(1))->method('get')->willReturn([]);
        return $serviceManager;
    }

    /**
     * @return Request|\PHPUnit_Framework_MockObject_MockObject|
     */
    private function createRequestMock()
    {
        $request = $this->getMock(Request::class);
        return $request;
    }

}
