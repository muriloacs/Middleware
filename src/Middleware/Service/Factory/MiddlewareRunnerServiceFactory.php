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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Config;
use Middleware\Service\MiddlewareRunnerService as Service;
use Middleware\MiddlewareManager;

class MiddlewareRunnerServiceFactory implements FactoryInterface
{
    private $middlewareManager;

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
        $middlewareManager = $this->getMiddlewareManager($config, $serviceManager);

        return function ($middlewareName) use ($middlewareManager) {
            return $middlewareManager->get($middlewareName);
        };
    }

    /**
     * Creates a custom ServiceManager.
     *
     * @param array $config
     * @param ServiceLocatorInterface $serviceManager
     *
     * @return MiddlewareManager
     */
    private function getMiddlewareManager(array $config, ServiceLocatorInterface $serviceManager)
    {
        if (!isset($this->middlewareManager)) {
            $middlewareConfig = new Config($config[Service::CONFIG]);
            $this->middlewareManager = new MiddlewareManager($middlewareConfig, $serviceManager);
        }

        return $this->middlewareManager;
    }
}