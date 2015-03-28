<?php
namespace Middleware;

use Closure;
use Zend\Http\PhpEnvironment\Request;

interface MiddlewareInterface
{
    public function __invoke(Request $request, Closure $next, Closure $redirect);
}

