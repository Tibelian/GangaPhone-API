<?php

namespace Tibelian;

use Tibelian\GangaPhoneApi\Service;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/_bootstrap.php';

define("BASE_DIR", __DIR__);
define("WEB_URL", 'https://gangaphone.tibelian.com');

$service = new Service();

$service->init();
