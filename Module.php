<?php
namespace Middleware;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\EventManager\EventInterface;
use Middleware\Listener\MiddlewareListener;

class Module implements 
        ConfigProviderInterface, 
        AutoloaderProviderInterface
{
    public function onBootstrap(EventInterface $e)
    {
        $eventManager = $e->getTarget()->getEventManager();
        $eventManager->attach(new MiddlewareListener());
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ]
            ],
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php'
            ]
        ];
    }
}