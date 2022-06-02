<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

/**
 * Access users data
 */
class UserRepository extends RepositoryBase {

    /**
     * insert query
     */
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
        $this->addQueryLog($query);
        if ($stmt->execute())
            return $stmt->insert_id;

        $this->addErrorLog($stmt->error);
        return -1;
    }

    /**
     * select query
     */
    public function find(array $userCond):?array {
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $query = '
            SELECT u.* 
            FROM user u '.$this->appendWhere($userCond, $conn);
        $result = $conn->query($query);
        $this->addQueryLog($query);
        if ($result) 
        {
            if ($result->num_rows == 0) {
                $this->addErrorLog('not found');
                return null;
            }
            $user = $result->fetch_assoc();

            $prodRepo = new ProductRepository();
            $user['products'] = $prodRepo->findOwner($user['id']);

            $msgRepo = new MessageRepository();
            $user['messages'] = $msgRepo->findAll($user['id']);

            return $user;
        } 
        else $this->addErrorLog($conn->error);
        return null;
    }

    /**
     * creates where condition
     */
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