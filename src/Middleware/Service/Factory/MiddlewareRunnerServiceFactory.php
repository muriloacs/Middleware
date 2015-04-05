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

use Middleware\Service\MiddlewareRunnerService as Service;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;

class MiddlewareRunnerServiceFactory implements FactoryInterface
{
    public $middlewareServiceManager;

    /**
     * Creates the MiddlewareService's instance.
     *
     * @param ServiceLocatorInterface $serviceManager
     *
     * @return MiddlewareRunnerService
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $request  = $serviceManager->get('Request');
        $response = $serviceManager->get('Response');
        $factory  = $this->createMiddlewareFactory($serviceManager);
        return new Service($request, $response, $factory);
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
        $config = $serviceManager->get('Config');   
        $middlewareServiceManager = $this->createMiddlewareServiceManager($config);

        return function ($middlewareName) use ($serviceManager, $middlewareServiceManager) {
            $middleware = $middlewareServiceManager->get($middlewareName);
            if ($middleware instanceof ServiceLocatorAwareInterface) {
                $middleware->setServiceLocator($serviceManager);
            }
            return $middleware;
        };
    }

    /**
     * Creates a custom ServiceManager.
     *
     * @param array $config
     * 
     * @return ServiceManager
     */
    private function createMiddlewareServiceManager(array $config)
    {
        if (!isset($this->middlewareServiceManager)) {
            $middlewareConfig = new Config($config[Service::CONFIG]);
            $this->middlewareServiceManager  = new ServiceManager($middlewareConfig);
        }

        return $this->middlewareServiceManager;
    }
}