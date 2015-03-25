<?php
namespace Middleware;

use Zend\Http\PhpEnvironment\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next, callable $redirect);
}

