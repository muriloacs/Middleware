<?php

/*
 * Murilo Amaral (http://muriloamaral.com)
 * Édipo Rebouças (http://edipo.com.br).
 *
 * @link https://github.com/muriloacs/Middleware
 *
 * @copyright Copyright (c) 2015 Murilo Amaral
 * @license The MIT License (MIT)
 *
 * @since File available since Release 1.0
 */

namespace MiddlewareTest\Service\Factory;

use Middleware\Service\Factory\MiddlewareRunnerServiceFactory;
use Middleware\Service\MiddlewareRunnerService as Service;

class MiddlewareRunnerServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MiddlewareRunnerServiceFactory
     */
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new MiddlewareRunnerServiceFactory();
    }

    public function testFactoryShouldCreateMiddlewareService()
    {
        $serviceManager = $this->createServiceManagerMock();
        $actual = $this->factory->createService($serviceManager);
        $this->assertInstanceOf('Middleware\Service\MiddlewareRunnerService', $actual);
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager|\PHPUnit_Framework_MockObject_MockObject|
     */
    private function createServiceManagerMock()
    {
        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceManager', array('get'));
        $serviceManager->expects($this->at(0))
            ->method('get')
            ->willReturn($this->createRequestStub());

        $serviceManager->expects($this->at(1))
            ->method('get')
            ->willReturn($this->createResponseStub());

        $serviceManager->expects($this->at(2))
            ->method('get')
            ->willReturn(array(
                Service::CONFIG => array()
            ));

        return $serviceManager;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Request|\PHPUnit_Framework_MockObject_MockObject|
     */
    private function createRequestStub()
    {
        $request = $this->getMock('Zend\Http\PhpEnvironment\Request');

        return $request;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Response|\PHPUnit_Framework_MockObject_MockObject|
     */
    private function createResponseStub()
    {
        $request = $this->getMock('Zend\Http\PhpEnvironment\Response');

        return $request;
    }


    /**
     * @covers \Middleware\Service\Factory\MiddlewareRunnerServiceFactory::createMiddlewareFactory
     */
    public function testMiddlewareFactory()
    {
        $serviceManager = $this->createServiceManagerMock();
        $middlewareServiceManager = $this->givenServiceManagerStub();

        $this->factory->middlewareServiceManager = $middlewareServiceManager;

        $runner = $this->factory->createService($serviceManager);

        $middlewareServiceManager->expects($this->at(0))
            ->method('get')
            ->with('test1')
            ->willReturn(function(){});

        $runner->run(array('test1'));
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager|\PHPUnit_Framework_MockObject_MockObject
     */
    public function givenServiceManagerStub()
    {
        $class = 'Zend\ServiceManager\ServiceManager';
        return $this->getMock($class, get_class_methods($class));
    }


}
