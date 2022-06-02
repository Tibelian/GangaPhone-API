<?php

namespace Tibelian;

use Tibelian\GangaPhoneApi\WebService;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/_bootstrap.php';

// two constants to define 
// 1. the project directory
// 2. the website url
define("BASE_DIR", __DIR__);
define("WEB_URL", 'https://gangaphone.tibelian.com');

// run the webservice
$service = new WebService();
$service->init();
