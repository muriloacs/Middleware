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

namespace Middleware\Exception;

class InvalidMiddlewareException extends \Exception
{
    public function __construct($plugin)
    {
        $type = is_object($plugin) ? get_class($plugin) : gettype($plugin);
        parent::__construct("Middleware of type $type is invalid; must implement Middleware\\MiddlewareInterface or be a callable");
    }
}
