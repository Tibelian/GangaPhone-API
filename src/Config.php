<?php

namespace Tibelian\GangaPhoneApi;

use Exception;

/**
 * Configuration loader
 */
class Config {

    // saves into memory objects
    private static array $memory = [];

    // load json files
    public static function get(string $filename):?array {

        // check if object is already in memory
        $data = self::loadFromMemory($filename);
        if ($data != null) 
            return $data; 

        // if not then load data from the config file
        try {
            $data = json_decode(file_get_contents('config/'.$filename.'.json'), true);
            self::saveToMemory($filename, $data);
            return $data;
        }
        catch(Exception $e) {
            return null;
        }

    }

    // load from memory if data isset
    private static function loadFromMemory(string $filename):?array {
        if (isset(self::$memory[$filename])) 
            return self::$memory[$filename];
        return null;
    }

    // saves to memory
    private static function saveToMemory(string $filename, array $data):void {
        self::$memory[$filename][] = $data;
    }

}