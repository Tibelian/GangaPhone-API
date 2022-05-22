<?php

namespace Tibelian\GangaPhoneApi;

use Exception;

class Config {

    private static array $memory = [];

    public static function get(string $filename):?array {

        $data = self::loadFromMemory($filename);
        if ($data != null) 
            return $data; 

        try {
            $data = json_decode(file_get_contents('config/'.$filename.'.json'), true);
            self::saveToMemory($filename, $data);
            return $data;
        }
        catch(Exception $e) {
            return null;
        }

    }

    private static function loadFromMemory(string $filename):?array {
        if (isset(self::$memory[$filename])) 
            return self::$memory[$filename];
        return null;
    }

    private static function saveToMemory(string $filename, array $data):void {
        self::$memory[$filename][] = $data;
    }

}