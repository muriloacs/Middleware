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

namespace Middleware;

use Middleware\Exception\InvalidMiddlewareException;
use Middleware\MiddlewareInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Manager for loading middlewares
 *
 * Does not define any middleware by default, but does add a validator.
 */
class MiddlewareManager extends AbstractPluginManager
{
    /**
     * We do not want arbitrary classes instantiated as middlewares.
     *
     * @var bool
     */
    protected $autoAddInvokableClass = false;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * service manager and event manager
     *
     * @param ConfigInterface $configuration
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(ConfigInterface $configuration, ServiceLocatorInterface $serviceManager)
    {
        parent::__construct($configuration);

        // Setting SL
        $this->setServiceLocator($serviceManager);

        // Pushing to bottom of stack to ensure this is done last
        $this->addInitializer(array($this, 'injectMiddlewareDependencies'), false);
    }

    /**
     * Inject required dependencies into the middleware.
     *
     * @param  MiddlewareInterface $middleware
     * @param  ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function injectMiddlewareDependencies(MiddlewareInterface $middleware, ServiceLocatorInterface $serviceLocator)
    {
        $parentLocator = $serviceLocator->getServiceLocator();

        if ($middleware instanceof ServiceLocatorAwareInterface) {
            $middleware->setServiceLocator($parentLocator->get('Zend\ServiceManager\ServiceLocatorInterface'));
        }

        if ($middleware instanceof EventManagerAwareInterface) {
            $events = $middleware->getEventManager();
            if (!$events instanceof EventManagerInterface) {
                $middleware->setEventManager($parentLocator->get('EventManager'));
            } else {
                $events->setSharedManager($parentLocator->get('SharedEventManager'));
            }
        }
    }

    /**
     * Validate the plugin
     *
     * Ensure we have a middleware.
     *
     * @param  mixed $plugin
     * @return true
     * @throws InvalidMiddlewareException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof MiddlewareInterface || is_callable($plugin)) {
            // we're okay
            return;
        }

        throw new InvalidMiddlewareException($plugin);
    }

    /**
     * Override: do not use peering service managers
     *
     * @param  string|array $name
     * @param  bool         $checkAbstractFactories
     * @param  bool         $usePeeringServiceManagers
     * @return bool
     */
    public function has($name, $checkAbstractFactories = true, $usePeeringServiceManagers = false)
    {
        return parent::has($name, $checkAbstractFactories, $usePeeringServiceManagers);
    }

    /**
     * Override: do not use peering service managers
     *
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return mixed
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = false)
    {
        return parent::get($name, $options, $usePeeringServiceManagers);
    }
}
