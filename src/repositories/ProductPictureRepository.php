<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

/**
 * Access product pictures
 */
class ProductPictureRepository extends RepositoryBase {

    /**
     * delete multiple query by product id
     */
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
        $this->addQueryLog($sql);
        $this->addErrorLog($mysqli->error);
    }

    /**
     * delete one picture query
     */
    public function deleteOne(int $picId):bool {

        // before we should delete the file

        $sql = "
            DELETE pp FROM product_picture pp 
            WHERE pp.id = $picId
        ";
        $mysqli = DatabaseManager::get()->getConn();
        $this->addQueryLog($sql);
        if ($mysqli->query($sql)) 
            return true;
        $this->addErrorLog($mysqli->error);
        return false;
    }

    /**
     * insert query knowing product and url
     */
    public function create(int $productId, string $url):?string {
        $query = "
            INSERT INTO product_picture(product_id, url)
            VALUES(?, ?)
        ";
        $database = DatabaseManager::get();
        $stmt = $database->getConn()->prepare($query);
        $stmt->bind_param("is", $productId, $url);
        $this->addQueryLog($query);
        if ($stmt->execute()) 
            return $url;
        $this->addErrorLog($stmt->error);
        return null;
    }

    /**
     * move file from request temp
     * to uploads directory, applying compresssion
     */
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
            //if (move_uploaded_file($file["tmp_name"], $target_file)) 
            if ($this->compress($file["tmp_name"], $target_file, 80))
                return WEB_URL . '/uploads/' . date('Y-m') . '/' . $randomName . '.' . $imgExtension;
            else
                $this->addErrorLog("Couldn't move the file to the uploads directory");
        } 
        else $this->addErrorLog("File is not an image.");
        return null;
    }

    /**
     * compress image file
     */
    private function compress(string $source, string $destination, int $quality):bool {
        $info = getimagesize($source);
        switch($info['mime']) {
            case 'image/jpeg':  $image = imagecreatefromjpeg($source);   break;
            case 'image/gif':   $image = imagecreatefromgif($source);    break;
            case 'image/png':   $image = imagecreatefrompng($source);    break;
            case 'image/bmp':   $image = imagecreatefrombmp($source);    break;
            case 'image/webp':  $image = imagecreatefromwebp($source);   break;
            case 'image/wbmp':  $image = imagecreatefromwbmp($source);   break;
        }
        return imagejpeg($image, $destination, $quality);
    }

}