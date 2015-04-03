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
     * @param Request $request
     * @param Closure $middlewareFactory
     */
    public function __construct(Request $request, Response $response, Closure $middlewareFactory)
    {
        $this->request = $request;
        $this->response = $response;
        $this->factory = $middlewareFactory;
    }

    /**
     * Run the middleware list
     * @param array $middlewareNames
     */
    public function run(array $middlewareNames)
    {
        if($middlewareNames) {
            $middlewareName = array_shift($middlewareNames);
            $factory = $this->factory;
            $middleware = $factory($middlewareName);
            $middleware($this->request, $this->response, $this->getNext($middlewareNames));
        }

    }

    /**
     * Call the next middleware
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
