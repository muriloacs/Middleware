<?php
namespace Middleware;

return array(
    'service_manager' => array(
        'factories' => array(
            'MiddlewareService' => __NAMESPACE__ . '\Service\Factory\MiddlewareServiceFactory'
        )
    ),
    'middlewares' => array(
        'global' => array(

        ),
    ),
);