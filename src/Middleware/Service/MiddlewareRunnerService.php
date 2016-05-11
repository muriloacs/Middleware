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

use Zend\ServiceManager\ServiceLocatorInterface;
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
     * @var array
     */
    protected $middlewareNames;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable $middlewareFactory
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, callable $middlewareFactory)
    {
        $this->request = $request;
        $this->response = $response;
        $this->factory = $middlewareFactory;
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
        $this->middlewareNames = $middlewareNames;
        $next = $this->getNextCallable();
        return $next();
    }

    /**
     * Return the current Request object (useful after all middlewares was executed)
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Return the current Response object (useful after all middlewares was executed)
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    protected function getNext()
    {
        $middlewareName = array_shift($this->middlewareNames);

        if (is_callable($middlewareName)) {
            $middleware = $middlewareName;
        } else {
            $middleware = call_user_func($this->factory, $middlewareName);
        }

        return $middleware;
    }

    /**
     * Calls the next middleware.
     *
     * @return callable
     */
    protected function getNextCallable()
    {
        $service = $this;

        return function (
            RequestInterface $request = null,
            ResponseInterface $response = null,
            callable $next = null
        ) use ($service) {
            $middleware = $service->getNext();

            // Allow Middleware to overwrite request and response
            if (null !== $request) {
                $service->request = $request;
            }
            if (null !== $response) {
                $service->response = $response;
            }

            // Middleware can return values for other middlewares and own Response for dispatcher
            return call_user_func($middleware, $service->request, $service->response, $service->getNextCallable());
        };
    }
}
