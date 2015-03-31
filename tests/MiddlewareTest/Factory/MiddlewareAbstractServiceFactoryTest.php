<?php

/**
 * Murilo Amaral (http://muriloamaral.com)
 * Édipo Rebouças (http://edipo.com.br).
 *
 * @link      https://github.com/muriloacs/Middleware
 *
 * @copyright Copyright (c) 2015 Murilo Amaral
 * @license   The MIT License (MIT)
 *
 * @since     File available since Release 1.0
 */

namespace MiddlewareTest\Factory;

use Middleware\Factory\MiddlewareAbstractServiceFactory;

class MiddlewareAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateServiceWithNameShouldReturnTrueWhenReceiveAConcreteClassNameOfMiddlewareInterface()
    {
        $abstractFactory = $this->givenAbstractFactory();
        $serviceLocator  = $this->givenServiceLocatorStub();
        $concreteClassName = $this->givenConcreteMiddlewareClassName();
        $actual = $abstractFactory->canCreateServiceWithName($serviceLocator, null, $concreteClassName);
        $this->assertTrue($actual);
    }

    public function testCanCreateServiceWithNameShouldReturnFalseWhenNotReceiveAConcreteClassNameOfMiddlewareInterface()
    {
        $abstractFactory = $this->givenAbstractFactory();
        $serviceLocator  = $this->givenServiceLocatorStub();
        $className = 'stdClass';
        $actual = $abstractFactory->canCreateServiceWithName($serviceLocator, null, $className);
        $this->assertFalse($actual);
    }

    public function testCreateServiceWithNameShouldReturnInstanceOfMiddlewareInterface()
    {
        $abstractFactory = $this->givenAbstractFactory();
        $serviceLocator  = $this->givenServiceLocatorStub();
        $concreteClassName = $this->givenConcreteMiddlewareClassName();
        $actual = $abstractFactory->createServiceWithName($serviceLocator, null, $concreteClassName);
        $this->assertInstanceOf('Middleware\MiddlewareInterface', $actual);
    }

    /**
     * @return MiddlewareAbstractServiceFactory
     */
    public function givenAbstractFactory()
    {
        return new MiddlewareAbstractServiceFactory();
    }

    /**
     * @return string
     */
    public function givenConcreteMiddlewareClassName()
    {
        return get_class($this->getMockForAbstractClass('Middleware\MiddlewareInterface'));
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function givenServiceLocatorStub()
    {
        return $this->getMockForAbstractClass('Zend\ServiceManager\ServiceLocatorInterface');
    }
}
