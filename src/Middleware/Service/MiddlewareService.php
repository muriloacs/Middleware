<?php
/**
 * Murilo Amaral (http://muriloamaral.com)
 * Édipo Rebouças (http://edipo.com.br)
 *
 * @link      https://github.com/muriloacs/Middleware
 * @copyright Copyright (c) 2015 Murilo Amaral
 * @license   The MIT License (MIT)
 * @since     File available since Release 1.0
 */

namespace Middleware\Service;

use Middleware\MiddlewareInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class MiddlewareService
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MvcEvent
     */
    private $event;

    /**
     * @var \Closure
     */
    private $middlewareFactory;

    /**
     * @param Request $request
     * @param \Closure  $middlewareFactory
     */
    public function __construct(Request $request, \Closure $middlewareFactory)
    {
        $this->request = $request;
        $this->middlewareFactory = $middlewareFactory;
    }

    /**
     * Instantiates middleware class and runs its handle() method.
     * @param string $middlewareClass
     */
    public function run($middlewareClass)
    {
        $middleware = $this->createMiddleware($middlewareClass);
        $middleware($this->getRequest(), $this->getNext(), $this->getRedirect());
    }

    /**
     * Called within controllers
     * @param string $middlewareClass Name of the middleware which is being called.
     */
    public function __invoke($middlewareName)
    {
        $this->run($middlewareName);
    }

    /**
     * @param string
     * @return \Closure
     */
    private function createMiddleware($middlewareClass)
    {
        $factory = $this->getMiddlewareFactory();
        $middleware = $factory($middlewareClass);
        return $middleware;
    }

    /**
     * @return \Closure
     */
    public function getMiddlewareFactory()
    {
        return $this->middlewareFactory;
    }


    /**
    * Returns $next() function.
    * @return Closure
    */
    private function getNext()
    {
       return function(Request $request) {
           $this->event->setRequest($request);
       };
    }

    /**
    * Returns $redirect() function.
    * @return \Closure
    */
    private function getRedirect()
    {
       return function($url = '/') {
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
     * @return MvcEvent
     */
    public function getEvent() 
    {
        return $this->event;
    }

    /***
     * @param \Closure $factory
     */
    public function setMiddlewareFactory(\Closure $middlewareClosureFactory)
    {
        $this->middlewareClosureFactory = $middlewareClosureFactory;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request) 
    {
        $this->request = $request;
    }

    /**
     * @param MvcEvent $event
     */
    public function setEvent(MvcEvent $event)
    {
        $this->event = $event;
    }

}