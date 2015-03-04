<?php
namespace Middleware;

return [
    'service_manager' => [
        'factories' =>[
            'MiddlewareService' => __NAMESPACE__ . '\Service\Factory\MiddlewareServiceFactory'
        ]
    ]
];