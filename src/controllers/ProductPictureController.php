<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Exception;
use Tibelian\GangaPhoneApi\Repository\ProductPictureRepository;

class ProductPictureController {

    public function create():void {
        
        // execute query
        $repo = new ProductPictureRepository();
        $ppId = $repo->create($_FILES['image'], $_POST['product_id']);

        // return the response
        echo json_encode([
            'status' => 'ok',
            'data' => $ppId,
            'query' => $repo->lastQuery
        ], JSON_PRETTY_PRINT);

    }

}
