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

namespace MiddlewareTest\Listener;

use Middleware\Listener\MiddlewareListener;

class MiddlewareListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testRunShouldRunConfiguredMiddlewareNames()
    {
        $listener           = $this->givenListener();
        $mvcEvent           = $this->givenMvcEventStub();
        $application        = $this->givenApplicationStub();
        $serviceManager     = $this->givenServiceManagerStub();
        $middlewareService  = $this->givenMiddlewareServiceStub();
        $routeMatch         = $this->givenRouteMatch();

        $mvcEvent->expects($this->at(0))
            ->method('getApplication')
            ->willReturn($application);

        $application->expects($this->once())
            ->method('getServiceManager')
            ->willReturn($serviceManager);

        $serviceManager->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('MiddlewareService'))
            ->willReturn($middlewareService);


        $serviceManager->expects($this->at(1))
            ->method('get')->with('Config')
            ->willReturn($this->givenMiddlewareConfig(array('Test'), array('KeyController' => array('Test3'))));


        $mvcEvent->expects($this->at(1))
            ->method('getRouteMatch')
            ->willReturn($routeMatch);

        $routeMatch->expects($this->at(0))
            ->method('getParam')
            ->willReturn('Key');

        $middlewareService->expects($this->at(0))->method('run')->with(array('Test', 'Test3'));

        $listener->onDispatch($mvcEvent);
    }

    /**
     * @return MiddlewareListener
     */
    private function givenListener()
    {
        return new MiddlewareListener();
    }

    /**
     * @param array $global
     *
     * @return array
     */
    private function givenMiddlewareConfig($global = array(), $local = array())
    {
        return array(
            MiddlewareListener::CONFIG => array(
                MiddlewareListener::CONFIG_GLOBAL => $global,
                MiddlewareListener::CONFIG_LOCAL => $local,
            ),
        );
    }

    /**
     * @return \Zend\Mvc\MvcEvent | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenMvcEventStub()
    {
        return $this->givenStub('Zend\Mvc\MvcEvent');
    }

    /**
     * @param strig $className
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenStub($className)
    {
        return $this->getMockForAbstractClass(
            $className,
            array(),
            '',
            false,
            false,
            true,
            get_class_methods($className)
        );
    }

    /**
     * @return \Zend\Mvc\Application | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenApplicationStub()
    {
        return $this->givenStub('Zend\Mvc\Application');
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenServiceManagerStub()
    {
        return $this->givenStub('Zend\ServiceManager\ServiceManager');
    }

    /**
     * @return \Middleware\Service\MiddlewareService | \PHPUnit_Framework_MockObject_MockObject
     */
    public function givenMiddlewareServiceStub()
    {
        return $this->givenStub('Middleware\Service\MiddlewareService');
    }

    public function givenRouteMatch()
    {
        return $this->givenStub('Zend\Mvc\Router\RouteMatch');
    }
}
