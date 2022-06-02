<?php

namespace Tibelian\GangaPhoneApi;

use mysqli;

/**
 * Singleton class DatabaseManager.
 * Opens one single connection
 * to the database using the
 * credentials from database.json
 */
class DatabaseManager {

    // the unique DatabaseManager object
    private static ?DatabaseManager $databaseManager = null;

    // mysql connection
    private mysqli $connection;

    // constructor is private
    // and it connects directly
    private function __construct() { 
        $this->connect();
    }

    // static getter of the singleton
    public static function get():DatabaseManager {
        if (self::$databaseManager == null) 
            self::$databaseManager = new DatabaseManager();
        return self::$databaseManager;
    }

    // connects using the mysqli
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

    // return the mysqli
    public function getConn():mysqli {
        return $this->connection;
    }

}