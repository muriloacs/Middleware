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

namespace Middleware\Listener;

use Middleware\Service\MiddlewareService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class MiddlewareListener implements ListenerAggregateInterface
{
    const CONFIG        = 'middlewares';
    const CONFIG_GLOBAL = 'global';
    const CONFIG_LOCAL  = 'local';
    const PROPERTY      = 'middleware';

    /**
     * @var array
     */
    protected $global = array();

    /**
     * @var array
     */
    protected $local = array();

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var MiddlewareService
     */
    protected $service;

    /**
     * Attachs onDispatch event.
     *
     * @param EventManagerInterface $eventManager
     */
    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach(
            MvcEvent::EVENT_DISPATCH,
            array($this, 'onDispatch'),
            100
        );
    }

    /**
     * Detachs events.
     *
     * @param EventManagerInterface $eventManager
     */
    public function detach(EventManagerInterface $eventManager)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($eventManager->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * On dispatch handles local and global middlewares.
     *
     * @param MvcEvent $event
     */
    public function onDispatch(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $config = $serviceManager->get('Config');

        $this->global = $config[self::CONFIG][self::CONFIG_GLOBAL];
        $this->local = $config[self::CONFIG][self::CONFIG_LOCAL];

        $this->service = $serviceManager->get('MiddlewareService');

        $this->service->setEvent($event);

        foreach ($this->getMiddlewareNames() as $middlewareName) {
            $this->service->run($middlewareName);
        }
    }

    /**
     * Return  global + local[Controller] middleware names
     * @return array
     */
    protected function getMiddlewareNames()
    {
        $controllerClass = $this->service->getEvent()->getRouteMatch()->getParam('controller').'Controller';

        $local = isset($this->local[$controllerClass]) ? $this->local[$controllerClass] : array();

        return array_merge($this->global, $local);
    }
}