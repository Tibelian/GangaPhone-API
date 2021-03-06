<?php

// this is the importer
// so itloads all the project files

require __DIR__ . '/Config.php';
require __DIR__ . '/WebService.php';
require __DIR__ . '/DatabaseManager.php';

require __DIR__ . '/controllers/LogController.php';
require __DIR__ . '/controllers/AuthController.php';
require __DIR__ . '/controllers/ProductPictureController.php';
require __DIR__ . '/controllers/ProductController.php';
require __DIR__ . '/controllers/MessageController.php';
require __DIR__ . '/controllers/UserController.php';

require __DIR__ . '/repositories/RepositoryBase.php';
require __DIR__ . '/repositories/UserRepository.php';
require __DIR__ . '/repositories/MessageRepository.php';
require __DIR__ . '/repositories/ProductRepository.php';
require __DIR__ . '/repositories/ProductPictureRepository.php';
