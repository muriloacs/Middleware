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

use Middleware\Service\Factory\MiddlewareServiceFactory;
use Zend\ServiceManager\ServiceManager;

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
        $this->assertInstanceOf('Middleware\Service\MiddlewareService', $actual);
    }

    /**
     * @return ServiceManager|\PHPUnit_Framework_MockObject_MockObject|
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
}
