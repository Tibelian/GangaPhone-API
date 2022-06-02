<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Exception;
use Tibelian\GangaPhoneApi\Repository\MessageRepository;

/**
 * Manage messages
 * CRUD functions
 */
class MessageController {

    /**
     * READ MULTIPLE
     * by user's id
     */
    public function findAll(int $userId):void
    {
        $repo = new MessageRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->findAll($userId),
            //'query' => $repo->getQueryLog,
            'error' => $repo->getErrorLog()
        ]);
    }

    /**
     * READ MULTPLE
     * by sender and receiver
     */
    public function findSpecific(int $from, int $to):void 
    {
        $repo = new MessageRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->findSpecific($from, $to),
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);
    }

    /**
     * READ ONE
     */
    public function findOne(int $id):void 
    {
        $repo = new MessageRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->findOne($id),
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);
    }

    /**
     * CREATE ONE
     */
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
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);
    }

}
