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

use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class MiddlewareRunnerService
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var callable
     */
    protected $factory;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable $middlewareFactory
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, callable $middlewareFactory)
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

        if (is_callable($middlewareName)) {
            $middleware = $middlewareName;
        }
        else {
            $middleware = call_user_func($this->factory, $middlewareName);
        }

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
