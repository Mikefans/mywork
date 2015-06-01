<?php
namespace Mapper;

abstract class Abstracts
{

    protected $_connection;

    protected $_query;

    protected $_name;

    protected $_primaryKey;

    protected $_callableMethod = array(
        "where",
        "whereOr",
        "whereIn",
        "whereNotIn",
        "select",
        "orderBy",
        'limit',
        'skip',
        'count'
    );

    protected static $_instances;

    /**
     * 获取mapper实例
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (! isset(static::$_instances[$class])) {
            static::$_instances[$class] = new $class();
        }
        return static::$_instances[$class];
    }

    public function __call($func, $arguments)
    {
        if (in_array($func, $this->_callableMethod, true)) {
            $db = $this->db();
            if ($db) {
                call_user_func_array(array(
                    $db,
                    $func
                ), $arguments);
            }
        } else {
            echo "call undefined method " . $func;
            die();
        }
        return $this;
    }

    public static function __callstatic($func, $arguments)
    {
        if (preg_match('/^where[A-Z][a-zA-Z0-9_]+$/', $func) && $arguments) {
            $instance = self::getInstance();
            $db = $instance->db();
            if ($db) {
                call_user_func_array(array(
                    $db,
                    $func
                ), $arguments);
            }
        } else {
            echo "call undefined method " . $func;
            die();
        }
        return $instance;
    }

    /**
     * 返回调试的sql语句
     */
    public function toSql()
    {
        $table = $this->getTableName();
        return $this->db()
            ->table($table)
            ->toSql();
    }

    /**
     * 取一条数据
     */
    public function first()
    {
        try {
            $table = $this->getTableName();
            $item = $this->db()
                ->table($table)
                ->first();
            if ($item) {
                return $this->_getMapModel($item);
            }
            return null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 取多条数据
     */
    public function get()
    {
        try {
            $table = $this->getTableName();
            
            $results = $this->db()
                ->table($table)
                ->get();
            $items = array();
            foreach ($results as $result) {
                $items[] = $this->_getMapModel($result);
            }
            return $items;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取结果集总数统计的结果
     * field为空则统计上次sql的total_result统计
     */
    public function count($field = null)
    {
        try {
            $table = $this->getTableName();
            return $this->db()
                ->table($table)
                ->count($field);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据某个字段取第一列数据的集合
     */
    public function lists($column)
    {
        try {
            $table = $this->getTableName();
            return $this->db()
                ->table($table)
                ->lists($column);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取数据结果模型
     */
    protected function _getMapModel($item)
    {
        return $item;
    }

    public function update($data, $conditions = array())
    {
        try {
            $table = $this->getTableName();
            return $this->db()
                ->table($table)
                ->update($data, $conditions);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function insert($data)
    {
        try {
            $table = $this->getTableName();
            $lastInsertId = $this->db()
                ->table($table)
                ->insert($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $lastInsertId;
    }

    public function replace($data)
    {
        try {
            $table = $this->getTableName();
            $lastInsertId = $this->db()
                ->table($table)
                ->replace($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $lastInsertId;
    }

    public function delete($conditions = array())
    {
        try {
            $table = $this->getTableName();
            return $this->db()
                ->table($table)
                ->delete($conditions);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取db链接
     */
    public function db()
    {
        $adapter = \Db\Mysql\Adapter::getInstance();
        if (! $this->_query) {
//             $adapter = new \Db\Mysql\Adapter();
            $db = $adapter->connect();
            $this->_query = $adapter->newQuery();
            
            
        }
        $adapter->setQuery($this->_query);
        return $adapter;
    }
}
