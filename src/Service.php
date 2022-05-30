<?php

namespace Tibelian\GangaPhoneApi;

use Bramus\Router\Router;

class Service {

    public function init() {

        // create Router instance
        $router = new Router();
        $routerConf = Config::get('router');

        // controllers namespace
        if (isset($routerConf['config']['namespace'])) 
            $router->setNamespace($routerConf['config']['namespace']);
        
        // error 404
        if (isset($routerConf['config']['set404'])) 
            $router->set404($routerConf['config']['set404']);

        // middleware
        foreach($routerConf['before'] as $route)
            $router->before($route['method'], $route['pattern'], $route['controller']);

        // all the routes
        foreach($routerConf['routes'] as $route) 
            $router->match($route['method'], $route['pattern'], $route['controller']);

        // run it!
        $router->run();

    }

}