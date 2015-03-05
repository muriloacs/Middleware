<?php
namespace Middleware\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Code\Reflection\ClassReflection as Reflection;
use Middleware\Service\MiddlewareService;
use Middleware\Entity\Middleware;
use Exception;

class MiddlewareListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

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
        $middlewareService = $serviceManager->get('MiddlewareService');

        if (!$middlewareService->getConfig()) {
            return;
        }

        $middlewareService->setEvent($event);

        $this->handleGlobal($middlewareService);
        $this->handleLocal($middlewareService);
    }

    /**
     * Handles global middlewares.
     * @param MiddlewareService $middlewareService
     */
    protected function handleGlobal(MiddlewareService $middlewareService)
    {
        $config  = $middlewareService->getConfig();

        if (!isset($config[Middleware::CONFIG_GLOBAL]) || !count($config[Middleware::CONFIG_GLOBAL])) {
            return;
        }

        $globals = $config[Middleware::CONFIG_GLOBAL];

        foreach ($globals as $middlewareClass) {
            $middlewareService->run($middlewareClass);
        }
    }

    /**
     * Handles local middlewares.
     * @param MiddlewareService $middlewareService
     */
    protected function handleLocal(MiddlewareService $middlewareService)
    {
        $controllerClass = $middlewareService->getEvent()->getRouteMatch()->getParam('controller') . 'Controller';

        try {
            $reflection = new Reflection($controllerClass);
            $reflection->getProperty(Middleware::PROPERTY);
            $controllerClass::$middleware = $middlewareService;
        }
        catch (Exception $e) {
            return;
        }
    }

}