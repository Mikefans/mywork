<?php
/**
 * 数据model层映射操作类
 * @author yuyang
 *
 */
namespace Hlg\Model;

use Exception;

abstract class Mapper_Abstract
{

    /**
     * 数据库连接
     */
    protected $_db;

    /**
     * 数据库连接配置
     */
    protected $_connection;

    /**
     * db query对象
     * 
     * @var unknown
     */
    protected $_query;

    /**
     * 表名
     */
    protected $_name;

    /**
     * 表主键,load方法中需要用
     */
    protected $_primaryKey;

    /**
     * 自增主键序列表
     *
     * @var unknown
     */
    protected $_sequenceTable;

    protected $_joinMapper;

    protected $_callableMethod = array(
        "where",
        "whereOr",
        "whereAndOr",
        "whereIn",
        "whereNotIn",
        "select",
        "distinct",
        "groupBy",
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
            throw new Exception("call undefined method " . $func);
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
            throw new Exception("call undefined method " . $func);
        }
        return $instance;
    }

    /**
     * 获取唯一自增主键序列id
     */
    public function sequenceId($step = 1)
    {
        if (! isset($this->_sequenceTable)) {
            throw new Exception("无法获取自增主键序列id");
        }
        return $this->db()
            ->table($this->_name)
            ->sequenceId($this->_sequenceTable, $step);
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
     * 简易获取单条记录
     *
     * @param
     *            array or string $value
     * @param
     *            array or string $field
     * @return mixed
     */
    public function load($value, $field = null)
    {
        if (is_null($value))
            return null;
        $where = array();
        if (is_null($field)) {
            $field = $this->getPrimaryKey();
            $where[$field] = $value;
        } else {
            if (is_array($field)) {
                if (! is_array($value)) {
                    $where[$field[0]] = $value;
                } else {
                    $num = count($field);
                    for ($i = 0; $i < $num; $i ++) {
                        $where[$field[$i]] = $value[$i];
                    }
                }
            } else {
                $where[$field] = $value;
            }
        }
        return $this->where($where)->first();
    }

    /**
     * inner join
     *
     * @param \Hlg\Model\Mapper_Abstract $mapper            
     * @param unknown $alias            
     * @param unknown $pKey
     *            主表关联主键
     * @param unknown $fKey
     *            关联外键
     * @return \Hlg\Model\Mapper_Abstract
     */
    public function innerJoin(\Hlg\Model\Mapper_Abstract $mapper, $alias, $condition)
    {
        $joinTable = $mapper->getTableName();
        $this->_joinMapper = array(
            $alias => $mapper
        );
        $this->db()->innerJoin($joinTable, $alias, $condition);
        return $this;
    }

    /**
     * left join
     *
     * @param \Hlg\Model\Mapper_Abstract $mapper            
     * @param unknown $alias            
     * @param unknown $pKey
     *            主表关联主键
     * @param unknown $fKey
     *            关联外键
     * @return \Hlg\Model\Mapper_Abstract
     */
    public function leftJoin(\Hlg\Model\Mapper_Abstract $mapper, $alias, $conditon)
    {
        $joinTable = $mapper->getTableName();
        $this->_joinMapper = array(
            $alias => $mapper
        );
        $this->db()->leftJoin($joinTable, $alias, $conditon);
        return $this;
    }

    /**
     * 取一条数据
     *
     * @throws Exception
     * @return \Hlg\Model\$this->modelClass Object:
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
            $error = $this->db()->error();
            if ($this->checkTableNotExists($error)) {
                return array();
            } else {
                throw $e;
            }
        }
    }

    /**
     * 取多条数据
     *
     * @throws Exception
     * @return multitype:\Hlg\Model\$this->modelClass |Object array:
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
            $error = $this->db()->error();
            if ($this->checkTableNotExists($error)) {
                return array();
            } else {
                throw $e;
            }
        }
    }

    /**
     * 获取结果集累加的结果
     * field为空则统计上次sql的total_result统计
     */
    public function sum($field,$distinct = false)
    {
        try {
            $table = $this->getTableName();
            return $this->db()
            ->table($table)
            ->sum($field,$distinct);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据某个字段取第一列数据的集合
     *
     * @param unknown $column            
     * @throws Exception
     * @return multitype:
     */
    public function lists($column)
    {
        try {
            $table = $this->getTableName();
            return $this->db()
                ->table($table)
                ->lists($column);
        } catch (Exception $e) {
            $error = $this->db()->error();
            if ($this->checkTableNotExists($error)) {
                return array();
            } else {
                throw $e;
            }
        }
    }

    /**
     * 获取数据结果模型
     *
     * @param unknown $item            
     */
    protected function _getMapModel($item)
    {
        if ($this->_joinMapper) {
            $alias = key($this->_joinMapper);
            $mapper = $this->_joinMapper[$alias];
            $model = $this->map($item);
            $queryMethod = "set" . ucfirst($alias) . "Model";
            $joinModel = $mapper->map($item);
            $model->{$queryMethod}($joinModel);
            $this->_joinMapper = array();
            return $model;
        } else {
            return $this->map($item);
        }
    }
    /**
     * 初始化db table设置
     */
    protected function _initTable()
    {
        $table = $this->getTableName();
        return $this->db()->table($table);
    }
    /**
     * 插入模型数据
     *
     * @param \Hlg\Model_Abstract $model            
     * @return boolean
     */
    public function insert(\Hlg\Model_Abstract $model)
    {
        $data = $model->toArray();
        if (! $data)
            return false;
        return $this->_insert($data);
    }

    /**
     * 更新插入模型数据
     *
     * @param \Hlg\Model_Abstract $model            
     * @return boolean
     */
    public function replace(\Hlg\Model_Abstract $model)
    {
        $data = $model->toArray();
        if (! $data)
            return false;
        return $this->_replace($data);
    }

    /**
     * 按主键更新model
     *
     * @param \Hlg\Model_Abstract $model            
     */
    public function update(\Hlg\Model_Abstract $model)
    {
        $modifiedData = $model->updateData();
        if (! $modifiedData)
            return false;
        $primaryKey = $this->getPrimaryKey();
        $update = $this->where(array(
            $primaryKey => $model->getData($primaryKey)
        ))
            ->_update($modifiedData);
        $model->refresh(); // refresh the dirty data
        return $update;
    }

    /**
     * 删除模型
     *
     * @param \Hlg\Model_Abstract $model            
     */
    public function delete(\Hlg\Model_Abstract $model)
    {
        $primaryKey = $this->getPrimaryKey();
        return $this->where(array(
            $primaryKey => $model->getData($primaryKey)
        ))
            ->_delete();
    }

    protected function _update($data, $conditions = array())
    {
        try {
            $table = $this->getTableName();
            return $this->db()
                ->table($table)
                ->update($data, $conditions);
        } catch (Exception $e) {
            $error = $this->db(0)->error();
            if ($this->checkTableNotExists($error)) {
                return array();
            } else {
                throw $e;
            }
        }
    }

    protected function _insert($data)
    {
        try {
            $table = $this->getTableName();
            $lastInsertId = $this->db()
                ->table($table)
                ->insert($data);
        } catch (Exception $e) {
            $error = $this->db()->error();
            if ($this->checkTableNotExists($error)) {
                $lastInsertId = $this->db()
                    ->table($table)
                    ->insert($data);
            } else {
                throw $e;
            }
        }
        return $lastInsertId;
    }

    protected function _replace($data)
    {
        try {
            $table = $this->getTableName();
            $lastInsertId = $this->db()
                ->table($table)
                ->replace($data);
        } catch (Exception $e) {
            $error = $this->db()->error();
            if ($this->checkTableNotExists($error)) {
                $lastInsertId = $this->db()
                    ->table($table)
                    ->replace($data);
            } else {
                throw $e;
            }
        }
        return $lastInsertId;
    }

    protected function _delete($conditions = array())
    {
        try {
            $table = $this->getTableName();
            return $this->db()
                ->table($table)
                ->delete($conditions);
        } catch (Exception $e) {
            $error = $this->db()->error();
            if ($this->checkTableNotExists($error)) {
                return array();
            } else {
                throw $e;
            }
        }
    }

    /**
     * 获取db链接
     *
     * @param string $divKey            
     * @return multitype: NULL
     */
    public function db($divKey = null)
    {
        if ($this->_connection) {
            $db = \Hlg\Db::connection($this->_connection);
            if (! $this->_query) {
                $this->_query = $db->newQuery();
            }
            $db->setQuery($this->_query); // 一个mapper对象对应一个db/query对象,每次获取db进行一次query设置
            return $db;
        }
        return null;
    }

    /**
     * 检查是否是因为表不存在而失败，是即创建新表
     *
     * @param array $error            
     * @param string $divKey            
     * @return PDOStatement boolean
     */
    public function checkTableNotExists($error)
    {
        if ($error[0] == '42S02' && $this->failRecreate()) {
            return $this->db()->query("CREATE TABLE IF NOT EXISTS " . $this->getTableName() . " LIKE " . $this->_name);
        }
        return false;
    }

    /**
     * 数据写入 $this->modelClass, ORM 处理
     *
     * @param array $data            
     * @return $this->modelClass
     */
    public function map($data)
    {
        $modelClass = $this->getModelClassName();
        
        return new $modelClass($data);
    }

    /**
     * 获取分表的主键值
     *
     * @param unknown $divKey            
     */
    public function getTableDivKey()
    {
        return $this->_divKey;
    }

    /**
     * 设置分表的主键值
     *
     * @return unknown
     */
    public function setTableDivKey($divKey)
    {
        $this->_divKey = $divKey;
    }

    /**
     * 是否进行失败重建(按日期分表的会用到)
     */
    public function failRecreate()
    {
        return false;
    }

    /**
     * 获取对应的model className
     */
    abstract public function getModelClassName();

    /**
     * 获取model对应的表主键的名称
     */
    abstract public function getPrimaryKey();

    /**
     * 获取model对应表名
     */
    abstract public function getTableName();
}
