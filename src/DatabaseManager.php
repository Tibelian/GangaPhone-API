<?php

namespace Tibelian\GangaPhoneApi;

use mysqli;

class DatabaseManager {

    private static ?DatabaseManager $databaseManager = null;
    private mysqli $connection;

    private function __construct() { 
        $this->connect();
     }

    public static function get():DatabaseManager {
        if (self::$databaseManager == null) 
            self::$databaseManager = new DatabaseManager();
        return self::$databaseManager;
    }

    private function connect():void {
        $conf = Config::get('database');
        $this->connection = new mysqli(
            $conf['host'],
            $conf['user'],
            $conf['pass'],
            $conf['name'],
            $conf['port']
        );
    }

    public function getConn():mysqli {
        return $this->connection;
    }

}