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
            array(
                MvcEvent::EVENT_BOOTSTRAP,
                MvcEvent::EVENT_ROUTE,
                MvcEvent::EVENT_DISPATCH,
                MvcEvent::EVENT_DISPATCH_ERROR,
                MvcEvent::EVENT_RENDER,
                MvcEvent::EVENT_FINISH
            ),
            array($this, 'onMvcEvent'),
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
    public function onMvcEvent(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $config = $serviceManager->get('Config');

        $this->config = $config[self::CONFIG];
        $this->service = $serviceManager->get('MiddlewareService');
        $this->service->setEvent($event);

        $this->handleGlobal($event->getName());
        $this->handleLocal();
    }

    /**
     * Handles global middlewares.
     */
    protected function handleGlobal($eventName)
    {
        $middlewaresNames = $this->config[self::CONFIG_GLOBAL][$eventName];

        foreach($middlewaresNames as $middlewaresName) {
            $this->service->run($middlewaresName);
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