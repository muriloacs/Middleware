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
use Middleware\MiddlewareInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class MiddlewareService
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var Closure
     */
    protected $factory;

    /**
     * @param Request $request
     * @param Closure $middlewareFactory
     */
    public function __construct(Request $request, Closure $middlewareFactory)
    {
        $this->request = $request;
        $this->factory = $middlewareFactory;
    }

    /**
     * Instantiates middleware class and runs its __invoke() method.
     *
     * @param string $middlewareName Name of the middleware which is being called.
     */
    public function run($middlewareName)
    {
        $middleware = $this->createMiddleware($middlewareName);
        $middleware($this->getRequest(), $this->getNext(), $this->getRedirect());
    }

    /**
     * Called within controllers.
     *
     * @param string $middlewareName Name of the middleware which is being called.
     */
    public function __invoke($middlewareName)
    {
        $this->run($middlewareName);
    }

    /**
     * Creates middleware.
     *
     * @param string $middlewareName Name of the middleware which is being called.
     *
     * @return MiddlewareInterface
     */
    protected function createMiddleware($middlewareName)
    {
        $factory = $this->getMiddlewareFactory();

        return $factory($middlewareName);
    }

    /**
     * Returns $next() function.
     *
     * TODO: make it useful
     *
     * @return Closure
     */
    protected function getNext()
    {
        $event = $this->event;

        return function (Request $request) use ($event) {
            $event->setRequest($request);
        };
    }

    /**
     * Returns $redirect() function.
     *
     * @return Closure
     */
    protected function getRedirect()
    {
        return function ($url = '/') {
           $response = $this->event->getResponse();
           $response->setStatusCode(Response::STATUS_CODE_307)
                    ->getHeaders()
                    ->addHeaderLine('Location', $url);
        };
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return MvcEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param MvcEvent $event
     */
    public function setEvent(MvcEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @return Closure
     */
    public function getMiddlewareFactory()
    {
        return $this->factory;
    }

    /***
     * @param Closure $factory
     */
    public function setMiddlewareFactory(Closure $factory)
    {
        $this->factory = $factory;
    }
}
