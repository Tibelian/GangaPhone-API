<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Exception;
use Tibelian\GangaPhoneApi\Repository\UserRepository;

/**
 * Manage user
 * CRUD functions
 */
class UserController {

    /**
     * CREATE ONE
     */
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
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $uId,
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);

    }

    /**
     * READ ONE
     */
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
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $data,
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);

    }

}
