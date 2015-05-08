<?php
/* 数库db操作父类
 * @yuyang
 */
namespace Hlg\Db;

abstract class Database
{

    protected $_connection;

    public function __construct($module)
    {
        $this->connect($module);
    }
    
    /*
     * get data from database @params $type string (first) $conditions array
     */
    abstract public function get();
    
    /*
     * get data from database @params $type string (all) $conditions array
     */
    abstract public function lists($column);
    
    /*
     * insert data into database @params $data array insert data $options array
     */
    abstract public function insert($data);
    
    /*
     * insert data into database with type replace @prams $data array insert data $options array
     */
    abstract public function replace($data);
    
    /*
     * update data in the database @params $data array update data $conditions array update conditions
     */
    abstract public function update($data, $conditions);
    
    /*
     * remove data in database @params $where array delete conditions
     */
    abstract public function delete($where);

    /**
     * 记录debug log
     * @param unknown $sql
     * @param unknown $bind
     */
    public function addDebugLog($sql,$bind){
        if(\Yaf\Registry::get("appConfig")->application->database->debug){
            \Hlg\Util::factory("debug")->setLogDir("sql");
            \Hlg\Util::factory("debug")->addLog($sql.";bind:{bindValue}",array("bindValue" => var_export($bind, true)));
        }
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if ($this->_connection) {
            $this->close();
        }
    }
}

