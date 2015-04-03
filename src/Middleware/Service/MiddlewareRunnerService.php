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

namespace Middleware\Service;

use Closure;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;

class MiddlewareRunnerService
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Closure
     */
    protected $factory;

    /**
     * @param Request  $request
     * @param Response $response
     * @param Closure  $middlewareFactory
     */
    public function __construct(Request $request, Response $response, Closure $middlewareFactory)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->factory  = $middlewareFactory;
    }

    /**
     * Runs the middleware list.
     * 
     * @param array $middlewareNames
     */
    public function run(array $middlewareNames)
    {
        if (!$middlewareNames) {
            return;
        }

        $middlewareName = array_shift($middlewareNames);
        $middleware     = is_callable($middlewareName) ? $middlewareName : call_user_func($this->factory, $middlewareName);
        call_user_func($middleware, $this->request, $this->response, $this->getNext($middlewareNames));
    }

    /**
     * Calls the next middleware.
     *
     * @param array $middlewareNames
     *
     * @return Closure
     */
    protected function getNext($middlewareNames)
    {
        $service = $this;
        return function () use ($service, $middlewareNames) {
            $service->run($middlewareNames);
        };
    }
}
