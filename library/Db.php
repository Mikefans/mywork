<?php
/**
 * db connection管理类
 * @author yuyang
 *
 */
namespace Hlg;

class Db
{

    public static $instance;

    private static $_connnections = array();

    private static $_adapter = "Mysql";

    public static function instance()
    {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置数据库类型
     * 
     * @param unknown $adapter            
     */
    public static function setAdapter($adapter)
    {
        self::$_adapter = $adapter;
    }

    /**
     * 获取某个module配置的connection
     * 
     * @param unknown $module            
     * @return multitype:
     */
    public static function connection($module)
    {
        if (! isset(self::$_connnections[$module]) || empty(self::$_connnections[$module])) {
            $adapterClass = "\Hlg\Db\\" . self::$_adapter . "\Adapter";
            $adapter = new $adapterClass($module);
            self::$_connnections[$module] = $adapter;
        }
        return self::$_connnections[$module];
    }

    /**
     * 关闭数据库connection
     * 
     * @param string $module            
     */
    public static function close($module = null)
    {
        if ($module) {
            if (isset(self::$_connnections[$module])) {
                self::$_connnections[$module]->close();
            }
        } else {
            foreach (self::$_connnections as $conn) {
                $conn->close();
            }
        }
    }
}