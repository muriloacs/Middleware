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
        $config  = $serviceManager->get('config');

        return new MiddlewareService($request, $config);
    }
}