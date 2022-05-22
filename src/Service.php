<?php

namespace Tibelian\GangaPhoneApi;

use Bramus\Router\Router;

class Service {

    private bool $logsEnabled = false;


    public function init() {

        // create Router instance
        $router = new Router();

        // middleware
        $router->before('GET|POST', '/.*', function() {
            if ($this->logsEnabled) $this->appendLog();
        });

        // define routes
        $router->get('/', function() {
            echo 'hola';
        });

        $router->post('/product/search', 
            '\Tibelian\GangaPhoneApi\Controller\ProductController@search');
        $router->get('/product/search', 
            '\Tibelian\GangaPhoneApi\Controller\ProductController@search');

        $router->get('/product/(\d+)', 
            '\Tibelian\GangaPhoneApi\Controller\ProductController@find');
            
        $router->post('/product/new', 
            '\Tibelian\GangaPhoneApi\Controller\ProductController@create');
        $router->post('/product/(\d+)', 
            '\Tibelian\GangaPhoneApi\Controller\ProductController@update');
            
        $router->post('/user/new', 
            '\Tibelian\GangaPhoneApi\Controller\UserController@create');
        $router->post('/user/find', 
            '\Tibelian\GangaPhoneApi\Controller\UserController@find');

        // run it!
        $router->run();

    }

    public function enableLogs(bool $doIt = true):void {
        $this->logsEnabled = $doIt;
    }

    private function appendLog():void {
        
        // filename and path
        $file = 'logs/' . date('Y-m-d') . '.json';
        
        // load prev info
        $content = [];
        if (file_exists($file)) 
            $content = json_decode(file_get_contents($file), true);
        
        // info saved
        $content[] = [
            'server' => $_SERVER,
            'post' => $_POST,
            'get' => $_GET,
            'files' => $_FILES,
            'stdin' => file_get_contents('php://stdin')
        ];

        // create file
        file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT));


    }

}