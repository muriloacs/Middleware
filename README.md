Middleware
============

Creates middleware layer on Zend Framework 2. Useful when it's necessary to make some work
between route and controller dispatch phases.


Installation
------------

#### With composer

1. Add this project in your composer.json:
    ```json
    "require": {
        "muriloamaral/middleware": "dev-master"
    }
    ```

2. Now tell composer to download Middleware by running the command:
    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php` file.
    ```php
    return array(
        'modules' => array(
            // ...
            'Middleware',
        ),
        // ...
    );
    ```

Configuration
-------------

1. On your config file set your global and local middlewares. For instance:
    ```bash
    Application/config/module.config.php
    ```
    ```php
    
    'middlewares' => array(
        'global' => array(
            'my.first.middleware',
            'my.three.middleware'
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'my.first.middleware' => 'Application\Middleware\First',
            'my.second.middleware' => 'Application\Middleware\Second',
        ),
        'services' => array(
            'my.three.middleware' => function($request, $next, $redirect) {
                // My code here. For instance:
    
                var_dump($request->getHeader('user-agent'));
    
            },
        ),
    ),
    ```

Usage
-----

1. Define your middleware classes:
    ```bash
    Application/src/Application/Middleware/
    ```
    ```php
    
    namespace Application\Middleware;
    
    class First
    {
        public function __invoke($request, $next, $redirect)
        {
            // My code here. For instance:
    
            var_dump($request->getHeader('user-agent'));
        }
    }
    ```
    ```php
    
    namespace Application\Middleware;
    
    class Second
    {
        public function __invoke($request, $next, $redirect)
        {
            // My code here. For instance:
    
            var_dump($request->getHeader('user-agent'));
        }
    }
    ```

#### Global scope
Middlewares on global scope will be executed everytime a request is made.

#### Local scope
Middlewares on local scope will be executed only if declared inside a controller. For instance:

```php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public static $middleware;

    public function __construct()
    {
        $middleware = self::$middleware;
        $middleware('my.second.middleware');
    }
}
    ```

In this case, `my.first.middleware` and `my.three.middleware`  will be always executed no matter what route is being called. Whereas `my.second.middleware` will be executed only when
Application\Controller\IndexController is called. Thus, if we access Application\Controller\IndexController both middlewares first and second will be executed.


Advanced usage
--------------

#### Inject Service Locator

1. It's also possible to access ServiceManager within your middleware classes. It's only necessary to implement ServiceLocatorAwareInterface. For instance:
    ```php
    
    namespace Application\Middleware;
    
    use Zend\ServiceManager\ServiceLocatorAwareInterface;
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    class First implements ServiceLocatorAwareInterface
    {
        protected $serviceLocator;
    
        public function __invoke($request, $next, $redirect)
        {
            // My code here. For instance:
            $config = $this->serviceLocator->get('config');
        }
    
        public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
        {
            $this->serviceLocator = $serviceLocator;
        }
    
        public function getServiceLocator()
        {
            return $this->serviceLocator;
        }
    }
    ```

#### Abstract Service Factory

If you not wanna declare the middlewares on service manager config key, you can use the abstract service factory provide by us.

1. Define your middleware class, you need implement the `Middleware\MiddlewareInterface`.
    ```php
    namespace Application\Middleware;
    
    use Closure;
    use Zend\Http\PhpEnvironment\Request;
    use Middleware\MiddlewareInterface;
    
    class First implements MiddlewareInterface
    {
        public function __invoke(Request $request, Closure $next, Closure $redirect)
        {
            // My code here.
        }
    }
    ```

2. Configure your middleware
    ```bash
    Application/config/module.config.php
    ```
    ```php
    'middlewares' => array(
        'global' => array(
            'Application\Middleware\First'
        ),
    )
    ```

3. Configure the abstract service factory
    ```bash
    Application/config/module.config.php
    ```
    ```php
    'service_manager' => array(
        'abstract_factories' => array(
            'Middleware\Factory\MiddlewareAbstractServiceFactory'
        ),
    )
    ```