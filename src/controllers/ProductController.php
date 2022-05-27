<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Exception;
use Tibelian\GangaPhoneApi\Repository\ProductRepository;
use Tibelian\GangaPhoneApi\Repository\ProductPictureRepository;

class ProductController {

    public function create():void {

        // obtain data
        $product = [];
        try {
            if (isset($_POST['product'])) 
                $product = json_decode($_POST['product'], true);
            else {
                echo json_encode([
                    'status' => 'ok',
                    'data' => -1,
                    'error' => 'miss "product" parameter'
                ]);
            }
        } catch(Exception $e) {}
        
        // crud manager
        $repo = new ProductRepository();
        $repoPic = new ProductPictureRepository();
        
        // execute insertion query
        $pId = $repo->create($product);

        // upload this picutres
        if ($pId != -1 && sizeof($_FILES) > 0) {
            foreach($_FILES as $pic) {
                $url = $repoPic->upload($pic);
                if ($url != null) 
                    $repoPic->create($pId, $url);
            }
        }
        
        // return the response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $pId != -1 ? $repo->find($pId) : -1, // could return only id
            'query' => $repo->getQueryLog() . $repoPic->getQueryLog(),
            'error' => $repo->getErrorLog() . $repoPic->getErrorLog()
        ]);

    }

    public function update(int $id):void {

        // obtain data
        $product = [];
        try {
            if (isset($_POST['product'])) 
                $product = json_decode($_POST['product'], true);
            else {
                echo json_encode([
                    'status' => 'ok',
                    'data' => -1,
                    'error' => 'miss "product" parameter'
                ]);
            }
        } catch(Exception $e) {}
        
        // execute query
        $repo = new ProductRepository();
        $ok = $repo->update($id, $product);

        // 
        if ($ok == false) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'ok',
                'data' => -1,
                //'query' => $repo->getQueryLog()
                'error' => $repo->getErrorLog()
            ]);
            return;
        }

        // the commands order is very important
        $repoPic = new ProductPictureRepository();

        // FIRST -- remove the old pictures
        $repoPic->delete($product['pictures'], $id);

        // SECOND -- upload new pictures
        if (sizeof($_FILES) > 0) {
            foreach($_FILES as $pic) {
                $url = $repoPic->upload($pic);
                if ($url != null) 
                    $repoPic->create($id, $url);
            }
        }

        // return the response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->find($id), // could return only id
            'query' => $repo->getQueryLog() . $repoPic->getQueryLog(),
            'error' => $repo->getErrorLog() . $repoPic->getErrorLog()
        ]);

    }

    public function updateVisits(int $id):void {
        
        // execute query
        $repo = new ProductRepository();
        $ok = $repo->upVisits($id);
        
        header('Content-Type: application/json');
        if ($ok == false)
            echo json_encode([
                'status' => 'ok',
                'data' => -1,
                //'query' => $repo->getQueryLog(),
                'error' => $repo->getErrorLog()
            ]);
        else
            echo json_encode([
                'status' => 'ok',
                'data' => $id
            ]);
    }

    public function find(int $id):void {

        // execute query
        $repo = new ProductRepository();
        $data = $repo->find($id);
        
        // return the response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $data,
            //'query' => $repo->getQueryLog,
            'error' => $repo->getErrorLog()
        ]);

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
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $data,
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);
        
    }

    public function delete(int $productId):void
    {
        $repo = new ProductRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->delete($productId),
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);
    }

}
