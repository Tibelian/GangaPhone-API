<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Tibelian\GangaPhoneApi\Repository\ProductPictureRepository;

class ProductPictureController {

    public function create():void
    {
        $repo = new ProductPictureRepository();
        $ppId = $repo->create($_POST['product_id'], $repo->upload($_FILES));
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $ppId,
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);
    }

    public function delete(int $pictureId):void
    {
        $repo = new ProductPictureRepository();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'data' => $repo->deleteOne($pictureId),
            //'query' => $repo->getQueryLog(),
            'error' => $repo->getErrorLog()
        ]);
    }

}
