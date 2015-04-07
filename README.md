[![Build Status](https://travis-ci.org/muriloacs/Middleware.svg?branch=master)](https://travis-ci.org/muriloacs/Middleware) [![Coverage Status](https://coveralls.io/repos/muriloacs/Middleware/badge.svg?branch=master)](https://coveralls.io/r/muriloacs/Middleware?branch=master)

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
        'my.first.middleware'
    ),
    'local' => array(
        'Application\Controller\IndexController' => array(
            'my.second.middleware'  
        )
    ),
    'invokables' => array(
        'my.first.middleware' => 'Application\Middleware\First',
        'my.second.middleware' => 'Application\Middleware\Second',
    )
),
// ...
```

#### Global scope
Middlewares on global scope will be run everytime a request is made.

#### Local scope
Middlewares on local scope will be run only if the executed controller declares a middleware.

#### This case
In this case, `my.first.middleware` will be always executed no matter what route is being called. Whereas `my.second.middleware` will be executed only when
Application\Controller\IndexController is being called. Thus, if we access Application\Controller\IndexController first and second middlewares will be executed.
P.S: global middlewares are run before local middlewares.

Usage
-----

Once you have configured your middlewares, just define their classes:

```bash
module/Application/src/Application/Middleware/First.php
```
```php

namespace Application\Middleware;

class First
{
    public function __invoke($request, $response, $next)
    {
        // My code here. For instance:

        var_dump($request->getHeader('user-agent'));

        $next(); // call the next middleware

        // Run code after all middlewares run
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
    public function __invoke($request, $response, $next)
    {
        // My code here. For instance:

        var_dump($request->getHeader('user-agent'));
        
        $next(); // call the next middleware

        // Run code after all middlewares run
    }
}
```

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

    public function __invoke($request, $response, $next)
    {
        // My code here. For instance:
        $config = $this->serviceLocator->get('config');
        $next();
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

#### Inject Event Manager

It's also possible to access EventManager within your middleware classes. It's only necessary to implement EventManagerAwareInterface. For instance:

```bash
module/Application/src/Application/Middleware/First.php
```
```php

namespace Application\Middleware;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class First implements EventManagerAwareInterface
{
    protected $eventManager;

    public function __invoke($request, $response, $next)
    {
        // My code here. For instance:
        $this->getEventManager()->trigger('myEvent', $this);
        
        $next();
    }

    public function getEventManager()
    {
        if (!$this->eventManager) { 
             $this->setEventManager(new EventManager(__CLASS__)); 
        }
        return $this->eventManager;
    }

    public function setEventManager(EventManagerInterface $eventManager) 
    {
        $this->eventManager = $eventManager;
    }
}
```

#### Abstract Service Factory

If you don't want to declare middlewares inside your service manager config key, you can use the abstract service factory provided by us.

1. Define your middleware class, you need to implement `Middleware\MiddlewareInterface`.
    ```bash
    module/Application/src/Application/Middleware/First.php
    ```
    ```php

    namespace Application\Middleware;
    
    use Closure;
    use Zend\Http\PhpEnvironment\Request;
    use Zend\Http\PhpEnvironment\Response;
    use Middleware\MiddlewareInterface;
    
    class First implements MiddlewareInterface
    {
        public function __invoke(Request $request, Response $response, Closure $next)
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
            'Application\Middleware\First'
        )
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

#### Configuration

You can provide any callable as a middleware name. Such as functions, static methods and so on. For instance:

```php
// ...
'middlewares' => array(
    'global' => array(
        'my.first.middleware',
        'my.second.middleware',
        'MyNamespace\MyClass::MyStaticMethod', // Static method sample
        function ($request, $response, $next) // Function sample
        {
            var_dump($request->getHeader('user-agent'));
            $next();    
        }
    ),
    'local' => array(
        'Application\Controller\IndexController' => array(
            'my.third.middleware'        
        ),
    ),
    // ...
),   
// ...
``` 


Practical usage
---------------

TODO...