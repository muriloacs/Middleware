<?php
namespace Middleware\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Exception\ParseException;
use Zend\Mvc\MvcEvent;
use Zend\Code\Reflection\ClassReflection as Reflection;
use Middleware\Service\MiddlewareService;
use Middleware\Entity\Middleware;
use Exception;

class MiddlewareListener implements ListenerAggregateInterface
{
    protected $globalConfig = array();
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

        $this->initConfig($serviceManager);

        $this->service = $serviceManager->get('MiddlewareService');

        if (!$this->service->getConfig()) {
            return;
        }

        $this->service->setEvent($event);
        $this->handleGlobal();
        $this->handleLocal();
    }

    private function initConfig($serviceManager) {
        $config = $serviceManager->has(Middleware::CONFIG) ?  $serviceManager->get(Middleware::CONFIG) : array();
        $this->globalConfig = isset($config[Middleware::CONFIG_GLOBAL]) ? $config[Middleware::CONFIG_GLOBAL] : array();
    }

    /**
     * Handles global middlewares.
     */
    protected function handleGlobal()
    {
        foreach ($this->globalConfig as $middlewareClass) {
            $this->service->run($middlewareClass);
        }
    }

    /**
     * Handles local middlewares.
     */
    protected function handleLocal()
    {
        $controllerClass = $this->service->getEvent()->getRouteMatch()->getParam('controller') . 'Controller';

        try {
            $controllerClass::${Middleware::PROPERTY} = $this->service;
        }
        catch (\Exception $e) {
            return;
        }
    }

}