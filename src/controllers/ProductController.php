<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Exception;
use Tibelian\GangaPhoneApi\Repository\ProductRepository;
use Tibelian\GangaPhoneApi\Repository\ProductPictureRepository;

class ProductController {

    public function create():void {

        // obtain data
        $product = []; $pictures = [];
        try {
            if (isset($_POST['product'])) 
                $product = json_decode($_POST['product'], true);
        } catch(Exception $e) {}
        
        // execute query
        $repo = new ProductRepository();
        $pId = $repo->create($product);

        if ($pId != -1 && sizeof($_FILES) > 0) {
            $repoPic = new ProductPictureRepository();
            foreach($_FILES as $pic) {
                $url = $repoPic->upload($pic);
                if ($url != null) 
                    $repoPic->create($pId, $url);
            }
        }

        // return the response
        echo json_encode([
            'status' => 'ok',
            'data' => $pId,
            'query' => $repo->lastQuery,
            'error' => $repo->error
        ], JSON_PRETTY_PRINT);

    }

    public function find(int $id):void {

        // execute query
        $repo = new ProductRepository();
        $data = $repo->find($id);
        
        // return the response
        echo json_encode([
            'status' => 'ok',
            'data' => $data,
            'query' => $repo->lastQuery
        ], JSON_PRETTY_PRINT);

    }

    public function search():void {
        
        // obtain request data
        $filter = [];
        try {
            if (isset($_POST['filter'])) 
                $filter = json_decode($_POST['filter'], true);
        } catch(Exception $e){}
        // execute query
        $repo = new ProductRepository();
        $data = $repo->search($filter);
        // return the response
        echo json_encode([
            'status' => 'ok',
            'data' => $data,
            'query' => $repo->lastQuery
        ], JSON_PRETTY_PRINT);
        
    }

}
