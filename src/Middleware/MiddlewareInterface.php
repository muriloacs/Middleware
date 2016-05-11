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

namespace Middleware;

use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

interface MiddlewareInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next);
}
