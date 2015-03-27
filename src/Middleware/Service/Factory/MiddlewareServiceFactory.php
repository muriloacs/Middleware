<?php
namespace Middleware\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Middleware\Service\MiddlewareService;

class MiddlewareServiceFactory implements FactoryInterface
{
    /**
     * Creates the MiddlewareService's instance.
     * @param  ServiceLocatorInterface $serviceManager 
     * @return MiddlewareService
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $request = $serviceManager->get('request');
        $factory = $this->createMiddlewareFactory($serviceManager);
        $middlewareService = new MiddlewareService($request, $factory);
        return $middlewareService;
    }

    private function createMiddlewareFactory(ServiceLocatorInterface $serviceManager)
    {
        return function($middlewareClass) use($serviceManager){
            if(!$this->serviceManager->has($middlewareClass)) {
                if(class_exists($middlewareClass)) {
                    $this->serviceManager->setInvokableClass($middlewareClass, $middlewareClass);
                }
                else {
                    throw new \InvalidArgumentException("Class or Service $middlewareClass not found");
                }
            }
            return $serviceManager->get($middlewareClass);
        };
    }
}