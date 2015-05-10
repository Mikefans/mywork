<?php
namespace Db;

class Db
{

    public static $instance;

    private static $_connnections = '';

    public static function instance()
    {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function connection()
    {
        if ( empty(self::$_connnections)) {
            $adapter = new \Db\Mysql\Adapter();
            self::$_connnections = $adapter;
        }
        return self::$_connnections;
    }

    public static function close()
    {
        if (!empty(self::$_connnections)) {
            self::$_connnections->close();
        }
    }
}