<?php

$header = <<<EOF
Murilo Amaral (http://muriloamaral.com)
Édipo Rebouças (http://edipo.com.br).

@link https://github.com/muriloacs/Middleware

@copyright Copyright (c) 2015 Murilo Amaral
@license The MIT License (MIT)

@since File available since Release 1.0
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->fixers(array('header_comment','long_array_syntax','ordered_use'))
    ->finder(Symfony\CS\Finder\DefaultFinder::create()
        ->in(__DIR__));