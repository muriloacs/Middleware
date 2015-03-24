<?php

namespace MiddlewareTest\Service;

use Middleware\MiddlewareInterface;
use Middleware\Service\MiddlewareService;
use Middleware\Entity\Middleware;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MiddlewareServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MiddlewareService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new MiddlewareService(
            $this->createRequestMock(),
            []
        );
        $this->service->setServiceLocator($this->createServiceLocatorMock());

    }

    /**
     * @return Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock()
    {
        $request = $this->getMock(Request::class);
        return $request;
    }

    /**
     * @return ServiceLocatorAwareInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createServiceLocatorMock()
    {
        $request = $this->getMock(ServiceLocatorInterface::class);
        return $request;
    }

    public function testRunShouldCallHandleMethodFromMiddleware()
    {
        $this->service->run(StubMiddleware::class);
        $this->assertTrue(StubMiddleware::$callConstructor);
        $this->assertTrue(StubMiddleware::$callSetServiceLocator);
        $this->assertTrue(StubMiddleware::$callHandle);
    }

}

class StubMiddleware implements MiddlewareInterface, ServiceLocatorAwareInterface
{
    public static $callConstructor = false;
    public static $callHandle = false;
    public static $callSetServiceLocator = false;

    public function __construct()
    {
        self::$callConstructor = true;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        self::$callSetServiceLocator = true;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        // TODO: Implement getServiceLocator() method.
    }


    public function handle(Request $request, callable $next, callable $redirect)
    {
        self::$callHandle = true;
    }

}