<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

class UserRepository {

    public string $lastQuery = "";
    public string $error = "";

    public function create(array $user):int {
        $query = "
            INSERT INTO user(username, password, email, phone, location)
            VALUES(?, PASSWORD(?), ?, ?, ?); ";
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $stmt = $conn->prepare($query);

        $uname = $user['username'];
        $pass = $user['password'];
        $email = $user['email'];
        $phone = $user['phone'];
        $loc = $user['location'];

        $stmt->bind_param('sssss', $uname, $pass, $email, $phone, $loc);
        if ($stmt->execute())
            return $stmt->insert_id;

        $this->error = $stmt->error;
        return -1;
    }

    public function find(array $userCond):?array {
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $query = '
            SELECT u.* 
            FROM user u '.$this->appendWhere($userCond, $conn);
        $result = $conn->query($query);
        $this->lastQuery = $query;
        if ($result) 
        {
            if ($result->num_rows == 0) {
                $this->error = 'not found';
                return null;
            }
            $user = $result->fetch_assoc();
            $user['products'] = [];
            
            $queryProducts = "
                SELECT p.*
                FROM product p
                WHERE p.user_id = {$user['id']}
            ";
            $resultProducts = $conn->query($queryProducts);
            $this->lastQuery .= $queryProducts;
            if ($resultProducts) {
                while ($p = $resultProducts->fetch_assoc()) {
                    $p['pictures'] = [];
                    $user['products'][] = $p;
                    $queryPictures = "
                        SELECT pp.*
                        FROM product_picture pp
                        WHERE pp.product_id = {$p['id']}
                    ";
                    $resultPictures = $conn->query($queryPictures);
                    $this->lastQuery .= $queryPictures;
                    if ($resultPictures) {
                        while ($pp = $resultPictures->fetch_assoc()) {
                            $p['pictures'][] = $pp;
                        }
                    }
                }
            } else {
                $this->error = $conn->error;
            }
            return $user;
        }   else {
            $this->error = $conn->error;
        }
        return null;
    }

    private function appendWhere(array $user, \mysqli $mysqli):string 
    {
        if (isset($user['id'])) 
            return "WHERE u.id = " . (int)$user['id'];

        if (isset($user['password']) && isset($user['username'])) {
            $uname = $mysqli->real_escape_string($user['username']);
            $pass = $mysqli->real_escape_string($user['password']);
            return "WHERE u.username = '$uname' AND u.password = PASSWORD('$pass')";
        }

        if (isset($user['username'])) {
            $uname = $mysqli->real_escape_string($user['username']);
            return "WHERE u.username = '$uname'";
        }
    }

}