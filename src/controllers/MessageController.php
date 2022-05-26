<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Exception;
use Tibelian\GangaPhoneApi\Repository\MessageRepository;

class MessageController {

    public function findAll(int $userId):void
    {
        $repo = new MessageRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->findAll($userId),
            //'query' => $repo->lastQuery,
        ]);
    }

    public function findSpecific(int $from, int $to):void 
    {
        $repo = new MessageRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->findSpecific($from, $to),
            //'query' => $repo->lastQuery,
        ]);
    }

    public function findOne(int $id):void 
    {
        $repo = new MessageRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->findOne($id),
            //'query' => $repo->lastQuery,
        ]);
    }

    public function create():void 
    {
        $message = [];
        try {
            if (isset($_POST['message'])) 
                $message = json_decode($_POST['message'], true);
            else {
                echo json_encode([
                    'status' => 'ok',
                    'data' => -1,
                    'error' => 'miss "message" parameter'
                ]);
            }
        } catch(Exception $e) {}

        $repo = new MessageRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->create($message),
            //'query' => $repo->lastQuery,
        ]);
    }

}
