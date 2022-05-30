<?php

namespace Tibelian\GangaPhoneApi\Controller;

class LogController {

    public function appendLog():void 
    {
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
            'files' => $_FILES
        ];

        // create file
        file_put_contents($file, 
            json_encode($content, JSON_PRETTY_PRINT));
    }

}