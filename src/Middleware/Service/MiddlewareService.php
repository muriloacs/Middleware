<?php
namespace Middleware\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Code\Reflection\ClassReflection as Reflection;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Exception;
use Middleware\Entity\Middleware;

class MiddlewareService implements ServiceLocatorAwareInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $config;

    /**
     * @var MvcEvent
     */
    private $event;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Dependencies injected.
     * @param Request $request
     * @param array $config
     */
    public function __construct(Request $request, array $config)
    {
        $this->request = $request;
        $this->config  = isset($config[Middleware::CONFIG]) && count($config[Middleware::CONFIG]) ?
                         $config[Middleware::CONFIG] :
                         null;
    }
    
    /**
    * Called within controllers, therefore handles with local middlewares.
    * @param string $name Name of the middleware which is being called.
    */
    public function __invoke($name)
    {
        if (!$name || !isset($this->config[Middleware::CONFIG_LOCAL][$name])) {
            return;
        }

        $middlewareClass = $this->config[Middleware::CONFIG_LOCAL][$name];
        $this->run($middlewareClass);
    }

    /**
     * Instantiates middleware class and runs its handle() method.
     * @param string $middlewareClass
     */
    public function run($middlewareClass)
    {
        try {
            $reflection = new Reflection($middlewareClass);
            $reflection->getMethod(Middleware::HANDLE_METHOD);

            $middleware = new $middlewareClass();

            if ($middleware instanceof ServiceLocatorAwareInterface) {
                $middleware->setServiceLocator($this->getServiceLocator());
            }

            $middleware->handle($this->getRequest(), $this->getNext(), $this->getRedirect());
        }
        catch (Exception $e) {
            return;
        }
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
    * @return Closure
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
     * @return array|null
     */
    public function getConfig() 
    {
        return $this->config;
    }

    /**
     * @return MvcEvent
     */
    public function getEvent() 
    {
        return $this->event;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request) 
    {
        $this->request = $request;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param MvcEvent $event
     */
    public function setEvent(MvcEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

}