<?php
namespace Middleware\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Middleware\Service\MiddlewareService;

class MiddlewareListener implements ListenerAggregateInterface
{
    const CONFIG = 'middlewares';
    const CONFIG_GLOBAL = 'global';
    const PROPERTY = 'middleware';

    /**
     * @var array
     */
    protected $config = array();
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
     * @param MvcEvent $event
     */
    public function onDispatch(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $config = $serviceManager->get('Config');

        $this->config = $config[self::CONFIG];
        $this->service = $serviceManager->get('MiddlewareService');
        $this->service->setEvent($event);

        $this->handleGlobal();
        $this->handleLocal();
    }

    /**
     * Handles global middlewares.
     */
    protected function handleGlobal()
    {
        $middlewares = $this->config[self::CONFIG_GLOBAL];

        foreach($middlewares as $middleware) {
            $this->service->run($middleware);
        }
    }

    /**
     * Handles local middlewares.
     */
    protected function handleLocal()
    {
        $controllerClass = $this->service->getEvent()->getRouteMatch()->getParam('controller') . 'Controller';
        if(property_exists($controllerClass, self::PROPERTY)) {
            $controllerClass::${self::PROPERTY} = $this->service;
        }
    }
}