<?php
namespace Middleware;

use Zend\Mvc\MvcEvent;

return array(
    'service_manager' => array(
        'factories' => array(
            'MiddlewareService' => __NAMESPACE__ . '\Service\Factory\MiddlewareServiceFactory'
        )
    ),
    'middlewares' => array(
        'global' => array(
            MvcEvent::EVENT_BOOTSTRAP => array(),
            MvcEvent::EVENT_ROUTE => array(),
            MvcEvent::EVENT_DISPATCH => array(),
            MvcEvent::EVENT_DISPATCH_ERROR => array(),
            MvcEvent::EVENT_RENDER => array(),
            MvcEvent::EVENT_FINISH => array(),
        ),
    ),
);