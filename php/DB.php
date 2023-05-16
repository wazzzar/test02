<?php

namespace php;

use \PDO;
use \Exception;

use function class_exists;

class DB {

    private static $connection;
    private static $statement;
    private static $isPDO;

    private static function connect() {
        if ( !self::$connection ) {
            self::$isPDO = class_exists("PDO") ? 1 : 0;
            if (self::$isPDO) {
                self::$connection = new PDO("mysql:host=localhost;dbname=test;charset=utf8", 'mysql', 'mysql', array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ));
            } else {
                throw new Exception("PDO class need");
            }
        }
    }
    
    private static function disconnect(){
        if ( self::$connection ) {
            //self::$connection::();
        }
    }

    public static function query($query) {
        self::connect();
        if( self::$isPDO ){
            self::$statement = self::$connection->prepare($query);
            self::$statement->execute();
        }else{
            throw new Exception("PDO class need");
        }
    }

    public static function fetch(){
        if( self::$isPDO ){
            return self::$statement->fetchAll();
        }else{
            throw new Exception("PDO class need");
        }
    }

    // query and fetch
    public static function qaf($query){
        self::connect();
        self::query($query);
        return self::fetch();
    }
}