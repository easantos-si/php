<?php

namespace lib;

class Router
{
    protected $routers = array(
        'site' => 'site',
        'admin' => 'admin'
    );

    protected $routerOnDefault = 'site';

    protected $onDefault = true;
}
