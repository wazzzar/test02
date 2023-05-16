<?php

namespace php;

class Request {

    private static $data;

    public static function get($entry)
    {
        if ( empty( self::$data ) ) self::$data = array_merge( $_GET, $_POST, $_FILES );
        if ( isset( self::$data[$entry] ) ) return self::$data[$entry];
        return '';
    }

    public static function cookie(string $string)
    {
        if ( isset( $_COOKIE[$string] ) ) return $_COOKIE[$string];
        return '';
    }

    public static function method(string $string): bool
    {
        return $_SERVER['REQUEST_METHOD'] == strtoupper($string);
    }
}