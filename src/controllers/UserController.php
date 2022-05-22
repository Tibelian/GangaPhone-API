<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Exception;
use Tibelian\GangaPhoneApi\Repository\UserRepository;

class UserController {

    public function create():void {

        // obtain data
        $user = []; 
        try {
            if (isset($_POST['user'])) 
                $user = json_decode($_POST['user'], true);
        } catch(Exception $e) {}
        
        // execute query
        $repo = new UserRepository();
        $uId = $repo->create($user);

        // return the response
        echo json_encode([
            'status' => 'ok',
            'data' => $uId,
            'query' => $repo->lastQuery,
            'error' => $repo->error
        ], JSON_PRETTY_PRINT);

    }

    public function find():void {

        // obtain data
        $user = []; 
        try {
            if (isset($_POST['user'])) 
                $user = json_decode($_POST['user'], true);
        } catch(Exception $e) {}

        // execute query
        $repo = new UserRepository();
        $data = $repo->find($user);
        
        // return the response
        echo json_encode([
            'status' => 'ok',
            'data' => $data,
            'query' => $repo->lastQuery,
            'error' => $repo->error
        ], JSON_PRETTY_PRINT);

    }

}
