<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

class ProductPictureRepository {

    public string $lastQuery = "";
    public string $error = "";

    public function delete(array $pictures, int $productId):void {
        $pIds = '';
        for($i = 0; $i < sizeof($pictures); $i++) {
            if ($i != 0) $pIds .= ",";
            $pIds .= (int)$pictures[$i]['id'];
        }
        $whereNot = "";
        if (!empty($pIds)) 
            $whereNot = "AND pp.id NOT IN ($pIds)";
        $sql = "
            DELETE pp FROM product_picture pp 
            INNER JOIN product p
            ON pp.product_id = p.id
            WHERE pp.product_id = $productId
            $whereNot
        ";
        $database = DatabaseManager::get();
        $mysqli = $database->getConn();
        $mysqli->query($sql);
        $this->lastQuery .= $sql;
        $this->error .= $mysqli->error;
    }

    public function deleteOne(int $picId):bool {

        // before we should delete the file

        $sql = "
            DELETE FROM product_picture pp 
            WHERE pp.id = $picId
        ";
        $mysqli = DatabaseManager::get()->getConn();
        if ($mysqli->query($sql))
            return true;
        else
            return false;
    }

    public function create(int $productId, string $url):?string {
        $query = "
            INSERT INTO product_picture(product_id, url)
            VALUES(?, ?)
        ";
        $database = DatabaseManager::get();
        $stmt = $database->getConn()->prepare($query);
        $stmt->bind_param("is", $productId, $url);
        if ($stmt->execute()) 
            return $url;
        return null;
    }

    public function upload(array $file):?string {

        // prepare directory
        $target_dir = BASE_DIR . '/uploads';
        $target_dir .= '/' . date('Y-m');
        if (!file_exists($target_dir))
            mkdir($target_dir);
        
        // prepare new file name
        $imgExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $randomName = sha1(date('h:i:s'));
        $target_file = $target_dir . '/' . $randomName . '.' . $imgExtension;

        // check if it is an image
        if(@getimagesize($file['tmp_name']) !== false) {
            // upload file
            if (move_uploaded_file($file["tmp_name"], $target_file)) 
                return WEB_URL . '/uploads/' . date('Y-m') . '/' . $randomName . '.' . $imgExtension;
            else
                $this->erro = "Couldn't move the file to the uploads directory";
        } else {
            $this->error = "File is not an image.";
        }

        return null;
    }

}