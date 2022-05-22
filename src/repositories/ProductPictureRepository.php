<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

class ProductPictureRepository {

    public string $lastQuery = "";
    public string $error = "";

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