<?php
/**
 * Murilo Amaral (http://muriloamaral.com)
 * Édipo Rebouças (http://edipo.com.br)
 *
 * @link      https://github.com/muriloacs/Middleware
 * @copyright Copyright (c) 2015 Murilo Amaral
 * @license   The MIT License (MIT)
 * @since     File available since Release 1.0
 */

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
            return $serviceManager->get($middlewareClass);
        };
    }
}