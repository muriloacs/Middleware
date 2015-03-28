<?php
namespace Middleware\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Middleware\Service\MiddlewareService;
use Middleware\Entity\Middleware;

class MiddlewareListener implements ListenerAggregateInterface
{
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

        if(isset($config[Middleware::CONFIG])) {

            $this->initConfig($config);

            $this->service = $serviceManager->get('MiddlewareService');
            $this->service->setEvent($event);

            $this->handleGlobal();
            $this->handleLocal();
        }
    }

    protected function initConfig(array $config)
    {
        $this->config = isset($config[Middleware::CONFIG]) ?  $config[Middleware::CONFIG] : array();
        $this->config[Middleware::CONFIG_GLOBAL] = isset($config[Middleware::CONFIG], $config[Middleware::CONFIG][Middleware::CONFIG_GLOBAL]) ? $config[Middleware::CONFIG][Middleware::CONFIG_GLOBAL] : array();
    }

    /**
     * Handles global middlewares.
     */
    protected function handleGlobal()
    {
        foreach($this->config[Middleware::CONFIG_GLOBAL] as $middlewareClass) {
            $this->service->run($middlewareClass);
        }
    }

    /**
     * Handles local middlewares.
     */
    protected function handleLocal()
    {
        $controllerClass = $this->service->getEvent()->getRouteMatch()->getParam('controller') . 'Controller';
        if(property_exists($controllerClass, Middleware::PROPERTY)) {
            $controllerClass::${Middleware::PROPERTY} = $this->service;
        }
    }
}