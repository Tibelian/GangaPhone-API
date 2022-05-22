<?php

namespace Tibelian\GangaPhoneApi\Repository;

use Tibelian\GangaPhoneApi\DatabaseManager;

class ProductRepository {

    public string $lastQuery = "";
    public string $error = "";

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
        if ($stmt->execute())
            return $stmt->insert_id;

        $this->error = $stmt->error;
        return -1;
    }

    public function find(int $id):array {
        $query = "
            SELECT p.*, p.id as pid, u.*, u.id as uid
            FROM product p LEFT JOIN user u ON p.user_id = u.id
            WHERE p.id = $id
        ";
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $result = $conn->query($query);
        if ($result) {
            $data = $result->fetch_assoc();
            $product = [];
            $product['id'] = $data['pid'];
            $product['name'] = $data['name'];
            $product['description'] = $data['description'];
            $product['date'] = $data['date'];
            $product['status'] = $data['status'];
            $product['price'] = $data['price'];
            $product['sold'] = $data['sold'];
            $product['visits'] = $data['visits'];
            $product['user'] = [];
            $product['user']['id'] = $data['uid'];
            $product['user']['username'] = $data['username'];
            $product['user']['location'] = $data['location'];
            $product['user']['email'] = $data['email'];
            $product['user']['phone'] = $data['phone'];
            $product['pictures'] = [];
            $query2 = "
                SELECT * FROM product_picture 
                WHERE product_id = " . $data['pid'];
            $result2 = $conn->query($query2);
            while ($data2 = $result2->fetch_assoc())
                $product['pictures'][] = $data2;
            return $product;
        }
        return [];
    }

    public function search(?array $filter = []):array {
        $query = "
            SELECT p.id as pid, p.name, p.description, p.status, p.date, p.price, 
            p.sold, p.visits, u.id as uid, u.location, pp.url AS thumbnail, u.username
            FROM product p LEFT JOIN user u ON p.user_id = u.id 
            LEFT JOIN product_picture pp ON p.id = pp.product_id 
            WHERE p.sold = 0 " . $this->applyFilter($filter) . "
        ";
        $entries = [];
        $database = DatabaseManager::get();
        $conn = $database->getConn();
        $result = $conn->query($query);
        if ($result)
            while($row = $result->fetch_assoc())
                $entries[] = $row;
        $this->lastQuery = $query;
        return $entries;
    }

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