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

namespace Middleware\Service\Factory;

use Middleware\Service\MiddlewareService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MiddlewareServiceFactory implements FactoryInterface
{
    /**
     * Creates the MiddlewareService's instance.
     *
     * @param ServiceLocatorInterface $serviceManager
     *
     * @return MiddlewareService
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $request = $serviceManager->get('Request');
        $factory = $this->createMiddlewareFactory($serviceManager);

        return new MiddlewareService($request, $factory);
    }

    /**
     * Creates Middleware Factory.
     *
     * @param ServiceLocatorInterface $serviceManager
     *
     * @return \Closure
     */
    private function createMiddlewareFactory(ServiceLocatorInterface $serviceManager)
    {
        return function ($middlewareName) use ($serviceManager) {
            return $serviceManager->get($middlewareName);
        };
    }
}
