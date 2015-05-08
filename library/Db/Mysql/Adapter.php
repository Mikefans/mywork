<?php
/*
 * mysql数据库操作类 @yuyang
 */
namespace Hlg\Db\Mysql;

use PDO;
use PDOStatement;
use PDOException;
use Exception;
use Hlg\Db\Mysql\Query;

class Adapter extends \Hlg\Db\DataBase
{

    protected $_module;

    protected $_connection;

    protected $_query;

    public $_error;

    /**
     * 失败重连次数
     *
     * @var unknown
     */
    const CONNECT_RETRY = 3;

    /**
     * 获取模块对应的数据库配置
     *
     * @param unknown $module            
     */
    public function getConfig($module)
    {
        $config = \Yaf\Registry::get("dbConfig")->database->{$module};
        if (! $config) {
            throw new Exception('model_dbname:' . $module . ' not exist');
        }
        $this->_module = $module;
        return $config->toArray();
    }

    /**
     * pdo连接数据库
     *
     * @param unknown $module            
     * @throws Exception
     * @return \PDO
     */
    public function connect($module)
    {
        $config = $this->getConfig($module);
        $dsn = 'mysql:host=' . $config['host'] . '; dbname=' . $config['dbname'];
        if (empty($this->_connection)) {
            $retry = self::CONNECT_RETRY;
            while ($retry) {
                $retry --;
                try {
                    $this->_connection = new PDO($dsn, $config['username'], $config['password'], array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    ));
                    $this->_connection->query("SET NAMES utf8");
                    break;
                } catch (PDOException $e) {
                    $code = $e->getCode();
                    if (! in_array($code, array(
                        1031,
                        1032,
                        1033,
                        1045
                    ))) {
                        // 如果不是用户名，密码相关错误，失败重连
                        continue;
                    }
                    throw new Exception('pdo connection failed.' . $e->getMessage());
                    break;
                }
            }
        }
        return $this->_connection;
    }

    /**
     * 数据库重新连接
     */
    public function reconnect()
    {
        $this->close();
        $this->connect($this->_module);
    }

    public function filter()
    {
        $self = $this;
        $filter = function ($val, $like = false) use($self)
        {
            if (get_magic_quotes_gpc()) {
                $val = stripslashes($val);
            }
            if (is_string($val) || ! is_numeric($val)) {
                $val = $self->_connection->quote($val);
            }
            if ($like === false) {
                /*
                $val = str_replace(array(
                    '%',
                    '_'
                ), array(
                    '\\%',
                    '\\_'
                ), $val);
                */
            }
            return $val;
        };
        return $filter;
    }

    /**
     * 获取新的query对象
     */
    public function newQuery()
    {
        $query = new Query();
        $query->filter = $this->filter();
        return $query;
    }

    /**
     * 设置db的query对象
     * 
     * @param unknown $query            
     */
    public function setQuery($query)
    {
        $this->_query = $query;
    }

    /**
     * 获取db的query对象
     * 
     * @param unknown $query            
     */
    public function getQuery()
    {
        if (! $this->_query) {
            $this->_query = $this->newQuery();
        }
        return $this->_query;
    }

    public function table($table)
    {
        $this->getQuery()->source($table);
        return $this;
    }

    public function query($sql, $bind = array())
    {
        $sql = trim($sql);
        if (preg_match("#^(update|delete)#is", $sql)) {
            if (stripos($sql, "where") === false) {
                throw new Exception("UPDATE_OR_DELETE_WHERE_IS_EMPTY");
            }
        }
        $this->getQuery()->clear(); // 每次运行完一次sql,清理临时变量
        $retry = self::CONNECT_RETRY;
        while ($retry) {
            $retry --;
            $stmt = $this->_connection->prepare($sql);
            if (! empty($bind)) {
                $sqlCount = substr_count($sql, "?");
                $bindCount = count($bind);
                if ($sqlCount != $bindCount) {
                    throw new Exception("query param number error:\n; [#SQL#]:" . $sql . ";bind:" . var_export($bind, true));
                }
                foreach ($bind as $key => $value) {
                    $i = $key + 1;
                    $paramType = $this->paramType($value);
                    $stmt->bindValue($i, $value, $paramType);
                }
            }
            /**
             * 记录debug日志
             */
            $this->addDebugLog($sql,$bind);
            $res = $stmt->execute();
            if ($res === false) {
                $this->_error = $stmt->errorInfo();
                if ($this->_error[0] == 2013 || $this->_error[0] == 2006) {
                    // 失败重连
                    $this->reconnect();
                    continue;
                } else {
                    $msg = isset($this->_error[2]) ? $this->_error[2] : "";
                    throw new Exception('stmt execute error:' . $msg . "\n" . '; [#SQL#]:' . $sql . ";bind:" . var_export($bind, true));
                }
            }
            return $stmt;
        }
    }

    public function paramType($value)
    {
        if (is_string($value)) {
            return PDO::PARAM_STR;
        }
        if (is_int($value)) {
            return PDO::PARAM_INT;
        }
        if (is_bool($value)) {
            return PDO::PARAM_BOOL;
        }
        if (is_null($value)) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }

    /**
     * 获取库里的所有tables
     */
    public function showTables()
    {
        $sql = "show tables";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 获取table信息
     *
     * @return mixed
     */
    public function tableInfo()
    {
        $source = $this->getQuery()->source;
        $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '" . $source . "'";
        return $this->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 展示某个table所有字段
     *
     * @return Ambigous <unknown, PDOStatement>
     */
    public function showColumns()
    {
        $source = $this->getQuery()->source;
        $dbName = $this->getConfig($this->_module)['dbname'];
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $source . "' AND TABLE_SCHEMA = '" . $dbName . "'";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 返回最后插入的id
     */
    public function lastInsertId()
    {
        return $this->_connection->lastInsertId();
    }

    /**
     * 获取自增唯一的序列id
     *
     * @param unknown $sequenceTable
     *            序列表
     * @param number $step            
     * @return string
     */
    public function sequenceId($sequenceTable, $step)
    {
        $sequenceKey = $this->getQuery()->source;
        $sql = "UPDATE " . $sequenceTable . " SET id=LAST_INSERT_ID(id+" . $step . ") WHERE sequence_key='" . $sequenceKey . "'";
        $this->query($sql);
        return $this->lastInsertId();
    }

    /**
     * 调用存储过程
     *
     * @param unknown $name            
     * @param unknown $params            
     */
    public function callProcedure($name, $params = array())
    {
        $param = implode(",", $params);
        $procedure = "CALL " . $name . "(" . $param . ")";
        $stmt = $this->query($procedure);
        return $stmt->fetch();
    }

    /**
     * Set the columns to be selected.
     *
     * @param array $columns            
     * @return $this
     */
    public function select($columns = array('*'))
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->getQuery()->field($columns);
        return $this;
    }

    public function distinct()
    {
        $this->getQuery()->distinct();
        return $this;
    }

    /**
     * 添加where条件,and组合
     *
     * @param unknown $conditions            
     * @return \Hlg\Db\Mysql\Adapter
     */
    public function where($conditions)
    {
        $this->getQuery()->whereAnd($conditions);
        return $this;
    }

    /**
     * 添加where条件,Or (a and b)
     *
     * @param unknown $conditions            
     * @return \Hlg\Db\Mysql\Adapter
     */
    public function whereOr($conditions)
    {
        $this->getQuery()->whereOr($conditions);
        return $this;
    }

    /**
     * 添加where条件,And (a or b)
     *
     * @param unknown $conditions            
     * @return \Hlg\Db\Mysql\Adapter
     */
    public function whereAndOr($conditions)
    {
        $this->getQuery()->whereAndOr($conditions);
        return $this;
    }

    /**
     * in 条件拼装
     * 
     * @param unknown $conditions            
     * @return \Hlg\Db\Mysql\Adapter
     */
    public function whereIn($conditions)
    {
        $this->getQuery()->whereIn($conditions);
        return $this;
    }

    /**
     * not in 条件拼装
     * 
     * @param unknown $conditions            
     * @return \Hlg\Db\Mysql\Adapter
     */
    public function whereNotIn($conditions)
    {
        $this->getQuery()->whereNotIn($conditions);
        return $this;
    }

    /**
     * left join拼装
     *
     * @param unknown $table
     *            join的table
     * @param unknown $alias
     *            别名
     * @param unknown $condition
     *            连接条件
     */
    public function leftJoin($table, $alias, $condition)
    {
        $this->getQuery()->leftJoin($table, $alias, $condition);
        return $this;
    }

    /**
     * inner join拼装
     *
     * @param unknown $table
     *            join的table
     * @param unknown $alias
     *            别名
     * @param unknown $condition
     *            连接条件
     */
    public function innerJoin($table, $alias, $condition)
    {
        $this->getQuery()->innerJoin($table, $alias, $condition);
        return $this;
    }

    /**
     * 获取一条数据
     *
     * @return mixed
     */
    public function first()
    {
        $sql = $this->toSql();
        $stmt = $this->query($sql['sql'], $sql['bind']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * 获取多条数据(non-PHPdoc)
     *
     * @see \Hlg\Db\Database::get()
     */
    public function get()
    {
        $sql = $this->toSql();
        $stmt = $this->query($sql['sql'], $sql['bind']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * 获取select的sql语句
     */
    public function toSql()
    {
        $sql = $this->getQuery()->select();
        $bind = $this->getQuery()->getBinding("where");
        return array(
            "sql" => $sql,
            "bind" => $bind
        );
    }

    /**
     * 获取结果集的累加值
     * @param unknown $field
     * @param string $distinct
     * @return number
     */
    public function sum($field,$distinct = false){
        $field = $distinct?"distinct ".$field:$field;
        $this->select("sum(" . $field . ")");
        $result = $this->first();
        if (empty($result)) {
            return 0;
        }
        $item = array_values($result);
        return (int)$item[0];
    }
    
    /**
     * 获取结果集的统计数目
     */
    public function count($field = null)
    {
        if ($field) {
            $this->select("count(" . $field . ")");
            $result = $this->first();
        } else {
            $stmt = $this->query("SELECT FOUND_ROWS()");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if (empty($result)) {
            return 0;
        }
        $item = array_values($result);
        return (int)$item[0];
    }

    /**
     * 获取结果集中某一个字段的列表(non-PHPdoc)
     *
     * @see \Hlg\Db\Database::lists()
     */
    public function lists($column)
    {
        $this->select($column);
        $sql = $this->getQuery()->select();
        $stmt = $this->query($sql, $this->getQuery()
            ->getBinding("where"));
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = array();
        if (! empty($items)) {
            foreach ($items as $item) {
                $result[] = $item[$column];
            }
        }
        return $result;
    }

    /**
     * 增加order by 查询
     *
     * @param unknown $column            
     * @param string $order            
     * @return \Hlg\Db\Mysql\Adapter
     */
    public function orderBy($column, $order = "DESC")
    {
        $this->getQuery()->orderBy(array(
            $column => $order
        ));
        return $this;
    }

    /**
     * 增加group by 查询
     *
     * @param unknown $column            
     * @return \Hlg\Db\Mysql\Adapter
     */
    public function groupBy($column)
    {
        $this->getQuery()->groupBy($column);
        return $this;
    }

    /**
     * 插入(单条/多条)数据(non-PHPdoc)
     *
     * @see \Hlg\Db\Database::insert()
     */
    public function insert($data)
    {
        $sql = $this->getQuery()->insert($data, 'INSERT');
        $this->query($sql, $this->getQuery()
            ->getBinding("insert"));
        $id = $this->_connection->lastInsertId();
        return $id ?  : true;
    }

    /**
     * 替换插入数据(non-PHPdoc)
     *
     * @see \Hlg\Db\Database::replace()
     */
    public function replace($data)
    {
        $sql = $this->getQuery()->insert($data, 'REPLACE');
        $this->query($sql, $this->getQuery()
            ->getBinding("insert"));
        return $this->_connection->lastInsertId();
    }

    /**
     * 删除数据(non-PHPdoc)
     *
     * @see \Hlg\Db\Database::delete()
     */
    public function delete($conditions)
    {
        $sql = $this->getQuery()->delete($conditions);
        return $this->query($sql, $this->getQuery()
            ->getBinding("where"));
    }

    /**
     * 更新数据(non-PHPdoc)
     *
     * @see \Hlg\Db\Database::update()
     */
    public function update($data, $conditions)
    {
        $sql = $this->getQuery()->update($data, $conditions);
        $bindings = array_merge($this->getQuery()->getBinding("update"), $this->getQuery()->getBinding("where"));
        return $this->query($sql, $bindings);
    }

    public function quote($val)
    {
        return $this->getQuery()->filter($val);
    }

    public function skip($count)
    {
        return $this->getQuery()->skip($count);
    }

    public function limit($count)
    {
        return $this->getQuery()->limit($count);
    }

    public function error()
    {
        return $this->_error;
    }

    public function close()
    {
        if ($this->_connection) {
            $this->_connection = null;
        }
    }
}
