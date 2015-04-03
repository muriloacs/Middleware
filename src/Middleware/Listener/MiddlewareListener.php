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

    /**
     * @var array
     */
    protected $listeners = array();

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
        $sm = $event->getApplication()->getServiceManager();
        $service = $sm->get('MiddlewareService');
        $config = $sm->get('Config');
        $controllerClass = $event->getRouteMatch()->getParam('controller').'Controller';

        $global = $config[self::CONFIG][self::CONFIG_GLOBAL];
        $local  = @$config[self::CONFIG][self::CONFIG_LOCAL][$controllerClass] ?: array();
        $middlewareNames = array_merge($global, $local);

        $service->run($middlewareNames);
    }
}
