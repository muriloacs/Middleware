Middleware
============

Creates middleware layer on Zend Framework 2. Useful when it's necessary to make some work
between route and controller dispatch phases.


Installation
------------

#### With composer

Add this project in your `composer.json`:

```json
"require": {
    "muriloamaral/middleware": "dev-master"
}
```

Now tell composer to download Middleware by running the command:

```bash
$ php composer.phar update
```

#### Post installation

Enabling it in your `config/application.config.php` file.

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

On your config file set your global and local middlewares. For instance:

```bash
module/Application/config/module.config.php
```
```php

// ...
'middlewares' => array(
    'global' => array(
        // ...
        'my.first.middleware',
        'my.three.middleware'
        // ...
    ),
),
// ...
'service_manager' => array(
    // ...
    'invokables' => array(
        // ...
        'my.first.middleware' => 'Application\Middleware\First',
        'my.second.middleware' => 'Application\Middleware\Second',
        // ...
    ),
    // ...
    'services' => array(
        // ...
        'my.three.middleware' => function($request, $next, $redirect) {
            // My code here. For instance:

            var_dump($request->getHeader('user-agent'));

        },
        // ...
    ),
    // ...
),
// ...
```

Usage
-----

Define your middleware classes:

```bash
module/Application/src/Application/Middleware/First.php
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
```bash
module/Application/src/Application/Middleware/Second.php
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

```bash
module/Application/src/Application/Middleware/First.php
```
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

It's also possible to access ServiceManager within your middleware classes. It's only necessary to implement ServiceLocatorAwareInterface. For instance:

```bash
module/Application/src/Application/Middleware/First.php
```
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

If you don't wanna declare the middlewares on service manager config key, you can use the abstract service factory provided by us.

1. Define your middleware class, you need implement the `Middleware\MiddlewareInterface`.
    ```bash
    module/Application/src/Application/Middleware/First.php
    ```
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
    module/Application/config/module.config.php
    ```
    ```php

    // ...
    'middlewares' => array(
        'global' => array(
            // ...
            'Application\Middleware\First'
            // ...
        ),
        // ...
    ),
    // ...
    ```

3. Configure the abstract service factory
    ```bash
    module/Application/config/module.config.php
    ```
    ```php

    // ...
    'service_manager' => array(
        // ...
        'abstract_factories' => array(
            // ...
            'Middleware\Factory\MiddlewareAbstractServiceFactory'
        ),
        // ...
    ),
    // ...
    ```