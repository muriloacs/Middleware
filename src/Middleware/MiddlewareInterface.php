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

use Closure;
use Zend\Http\PhpEnvironment\Request;

interface MiddlewareInterface
{
    public function __invoke(Request $request, Closure $next, Closure $redirect);
}