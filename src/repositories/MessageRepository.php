<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

/**
 * Access messages/chats
 */
class MessageRepository extends RepositoryBase {

    /**
     * insert one message query
     */
    public function create(array $message):int {
        $query = "
            INSERT INTO message(receiver_uid, sender_uid, content, date, is_read)
            VALUES(?, ?, ?, NOW(), 0);
        ";
        $db = DatabaseManager::get();
        $stmt = $db->getConn()->prepare($query);
        $receiverId = $message['to']['id'];
        $senderId = $message['from']['id'];
        $content = $message['content'];
        $stmt->bind_param("iis", $receiverId, $senderId, $content);
        $this->addQueryLog($query);
        if ($stmt->execute()) 
            return $stmt->insert_id;
        $this->addErrorLog($stmt->error);
        return -1;
    }

    /**
     * select all messages by user
     */
    public function findAll(int $userId):array {
        $data = [];
        $query = "
            SELECT m.*, s.username as sender_uname, r.username as receiver_uname
            FROM message m
            INNER JOIN user s
                ON s.id = m.sender_uid 
            INNER JOIN user r
                ON r.id = m.receiver_uid
            WHERE m.sender_uid = ?
                OR m.receiver_uid = ?
            GROUP BY m.id
            ORDER BY m.date ASC;
        ";
        $db = DatabaseManager::get();
        $stmt = $db->getConn()->prepare($query);
        $senderId = $userId;
        $receiverId = $userId;
        $stmt->bind_param("ii", $senderId, $receiverId);
        $this->addQueryLog($query);
        if ($stmt->execute())
        {
            $result = $stmt->get_result();
            while($m = $result->fetch_assoc()) {
                $data[] = [
                    'id' => (int) $m['id'],
                    'sender_uid' => (int) $m['sender_uid'],
                    'receiver_uid' => (int) $m['receiver_uid'],
                    'sender_uname' => $m['sender_uname'],
                    'receiver_uname' => $m['receiver_uname'],
                    'date' => $m['date'],
                    'content' => $m['content'],
                    'is_read' => (bool) $m['is_read'],
                ];
            }
        } else $this->addErrorLog($stmt->error);
        return $data;
    }

    /**
     * select all messages between two users
     */
    public function findSpecific(int $from, int $to):?array {
        $data = [];
        $query = "
            SELECT * FROM message
            WHERE sender_uid = ?
            OR receiver_uid = ?
            ORDER BY date DESC;
        ";
        $db = DatabaseManager::get();
        $stmt = $db->getConn()->prepare($query);
        $senderId = $from;
        $receiverId = $to;
        $stmt->bind_param("ii", $senderId, $receiverId);
        $this->addQueryLog($query);
        if ($stmt->execute())
        {
            $result = $stmt->get_result();
            while($m[] = $result->fetch_assoc()) {
                $data[] = [
                    'id' => (int) $m['id'],
                    'sender_uid' => (int) $m['sender_uid'],
                    'receiver_uid' => (int) $m['receiver_uid'],
                    'date' => $m['date'],
                    'content' => $m['content'],
                    'is_read' => (bool) $m['is_read'],
                ];
            }
        } else $this->addErrorLog($stmt->error);
        return $data;
    }

    /**
     * select one message by id
     */
    public function findOne(int $msgId):?array {
        $query = "
            SELECT * FROM message
            WHERE id = ?;
        ";
        $db = DatabaseManager::get();
        $stmt = $db->getConn()->prepare($query);
        $id = $msgId;
        $stmt->bind_param("i", $id);
        $this->addQueryLog($query);
        if ($stmt->execute())
        {
            $result = $stmt->get_result();
            $m = $result->fetch_assoc();
            return [
                'id' => (int) $m['id'],
                'sender_uid' => (int) $m['sender_uid'],
                'receiver_uid' => (int) $m['receiver_uid'],
                'date' => $m['date'],
                'content' => $m['content'],
                'is_read' => (bool) $m['is_read'],
            ];
        }
        $this->addErrorLog($stmt->error);
        return null;
    }


}