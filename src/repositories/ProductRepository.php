<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

/**
 * Access product data
 */
class ProductRepository extends RepositoryBase {

    /**
     * delete query one product
     * knowing his id
     */
    public function delete(int $id):bool
    {

        // pictures are deleted on cascade
        // but before we should remove the files

        $sql = "
            DELETE p FROM product p 
            WHERE p.id = $id
        ";
        $mysqli = DatabaseManager::get()->getConn();
        $this->addQueryLog($sql);
        if ($mysqli->query($sql))
            return true;
        $this->addErrorLog($mysqli->error);
        return false;
    }

    /**
     * update query
     */
    public function update(int $id, array $product):bool {
        $query = "
            UPDATE product
            SET name = ?, description = ?, 
            status = ?, sold = ?, price = ?
            WHERE id = ?;
        ";
        $database = DatabaseManager::get();
        $stmt = $database->getConn()->prepare($query);
        $name = $product['name'];
        $desc = $product['description'];
        $stat = $product['status'];
        $sold = $product['sold'];
        $price = $product['price'];
        $pId = $id;
        $stmt->bind_param("sssidi", $name, $desc, $stat, $sold, $price, $pId);

        $ok = $stmt->execute();
        $this->addQueryLog($query);
        $this->addErrorLog($stmt->error);
        return $ok;
    }

    /** 
     * update query increase visits
     */
    public function upVisits(int $productId) {
        $query = "
            UPDATE product 
            SET visits = visits + 1 
            WHERE id = ?;
        ";
        $database = DatabaseManager::get();
        $stmt = $database->getConn()->prepare($query);
        $id = $productId;
        $stmt->bind_param("i", $id);

        $ok = $stmt->execute();
        $this->addQueryLog($query);
        $this->addErrorLog($stmt->error);
        return $ok;
    }

    /**
     * insert query
     */
    public function create(array $product):int {
        $query = "
            INSERT INTO product(name, description, date, status, sold, visits, user_id, price)
            VALUES(?, ?, NOW(), ?, 0, 0, ?, ?); ";
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $stmt = $conn->prepare($query);

        $name = $product['name'];
        $desc = $product['description'];
        $status = $product['status'];
        $uid = $product['owner']['id'];
        $price = $product['price'];

        $stmt->bind_param('sssid', $name, $desc, $status, $uid, $price);
        $this->addQueryLog($query);
        if ($stmt->execute())
            return $stmt->insert_id;

        $this->addErrorLog($stmt->error);
        return -1;
    }

    /**
     * select query by id
     */
    public function find(int $id):array {
        $query = "
            SELECT p.*, p.id as pid, u.*, u.id as uid
            FROM product p LEFT JOIN user u ON p.user_id = u.id
            WHERE p.id = $id
        ";
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $result = $conn->query($query);
        $this->addQueryLog($query);
        if ($result) {
            $data = $result->fetch_assoc();
            $product = [
                'id' => (int) $data['pid'],
                'name' => $data['name'],
                'description' => $data['description'],
                'date' => $data['date'],
                'status' => $data['status'],
                'sold' => (bool) $data['sold'],
                'price' => (float) $data['price'],
                'visits' => (int) $data['visits'],
                'owner' => [
                    'id' => (int) $data['uid'],
                    'username' => $data['username'],
                    'location' => $data['location'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ],
                'pictures' => [] // next query
            ];
            $query2 = "
                SELECT * FROM product_picture 
                WHERE product_id = " . $data['pid'];
            $this->addQueryLog($query2);
            $result2 = $conn->query($query2);
            while ($data2 = $result2->fetch_assoc())
                $product['pictures'][] = [
                    'id' => (int) $data2['id'],
                    'url' => $data2['url']
                ];
            return $product;
        } else $this->addErrorLog($conn->error);
        return [];
    }

    /**
     * selcect query by owner
     */
    public function findOwner(int $uid):array {
        $products = [];
        $sql = "
            SELECT p.*
            FROM product p
            WHERE p.user_id = {$uid}
        ";
        $conn = DatabaseManager::get()->getConn();
        $result = $conn->query($sql);
        $this->addQueryLog($sql);
        if ($result) {
            while ($p = $result->fetch_assoc())
            {   
                $pictures = [];
                $sqlPics = "
                    SELECT pp.*
                    FROM product_picture pp
                    WHERE pp.product_id = {$p['id']}
                ";
                $resultPics = $conn->query($sqlPics);
                $this->addQueryLog($sqlPics);
                if ($resultPics) {
                    while ($pp = $resultPics->fetch_assoc())
                        $pictures[] = [
                            'id' => $pp['id'],
                            'url' => $pp['url']
                        ];
                } else $this->addErrorLog($conn->error);
                
                $p['pictures'] = $pictures;
                $products[] = $p;
            }
        } else $this->addErrorLog($conn->error);
        return $products;
    }

    /**
     * select query with condition
     */
    public function search(?array $filter = []):array {
        $query = "
            SELECT p.id as pid, p.name, p.description, p.status, p.date, p.price, p.sold, 
            p.visits, u.id as uid, u.location, pp.id as ppid, pp.url AS thumbnail, u.username, u.email, u.phone
            FROM product p LEFT JOIN user u ON p.user_id = u.id 
            LEFT JOIN product_picture pp ON p.id = pp.product_id 
            WHERE p.sold = 0 " . $this->applyFilter($filter) . "
        ";
        $entries = [];
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $result = $conn->query($query);
        $this->addQueryLog($query);
        if ($result)
            while($row = $result->fetch_assoc())
                $entries[] = [
                    'id' => (int) $row['pid'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'status' => $row['status'],
                    'date' => $row['date'],
                    'price' => (float) $row['price'],
                    'sold' => (bool) $row['sold'],
                    'visits' => (int) $row['visits'],
                    'owner' => [
                        'id' => (int) $row['uid'],
                        'username' => $row['username'],
                        'location' => $row['location'],
                        'email' => $row['email'],
                        'phone' => $row['phone']
                    ],
                    'pictures' => [
                        [
                            'id' => (int) $row['ppid'],
                            'url' => $row['thumbnail']
                        ]
                    ]
                ];
        else $this->addErrorLog($conn->error);
        $this->addQueryLog($query);
        return $entries;
    }

    /**
     * creates condition using custom filter
     */
    private function applyFilter(array $filter):string {
        if (sizeof($filter) == 0) return ' GROUP BY p.id ';
        $where = "";

        // status
        if (isset($filter['status'])) {
            $where .= " AND p.status IN (";
            for ($i = 0; $i < sizeof($filter['status']); $i++) {
                if ($i != 0) $where .= ", ";
                $where .= "'" . $filter['status'][$i] . "'";
            }
            $where .= ")";
        }

        // keyword
        if (isset($filter['keyword']))
            $where .= " 
                AND (p.name LIKE '%" . $filter['keyword'] . "%' OR 
                p.description LIKE '%" . $filter['keyword'] . "%')
            ";

        // location
        if (isset($filter['location']))
            $where .= " AND u.location LIKE '%". $filter['location'] . "%'";

        // price
        if ($filter['maxPrice'] > -1)
            $where .= " AND p.price < " . $filter['maxPrice'];
        if ($filter['minPrice'] > -1)
            $where .= " AND p.price > " . $filter['minPrice'];

        // group by product.id always
        // to prevent duplicated entries
        $where .= ' GROUP BY p.id ';

        // order
        if (isset($filter['orderBy'])) {
            $where .= ' ORDER BY ';
            switch ($filter['orderBy']) {
                case "date.asc": $where .= "p.date ASC"; break;
                case "date.desc": $where .= "p.date DESC"; break;
                case "price.asc": $where .= "p.price ASC"; break;
                case "price.desc": $where .= "p.price DESC"; break;
                case "featured": $where .= "p.visits DESC"; break;
            }
        }

        return $where;
    }

}