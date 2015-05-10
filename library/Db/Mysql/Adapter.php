<?php
namespace Db\Mysql;

use PDO;
use PDOStatement;
use PDOException;

class Adapter
{

    protected $_connection;

    protected $_query;

    const CONNECT_RETRY =3;
    
    public function __construct()
    {
        $this->connect();
    }

    /**
     * 获取模块对应的数据库配置
     */
    public function getConfig()
    {
        $config = new \Yaf\Config\Ini(BP. '/conf/db.ini', 'base');
        $config = $config->get("database")->shop;
        return $config->toArray();
    }

    /**
     * pdo连接数据库
     */
    public function connect()
    {
        $config = $this->getConfig();
        $dsn = 'mysql:host=' . $config['host'] . '; dbname=' . $config['dbname'];
        if (empty($this->_connection)) {
            $this->_connection = new PDO($dsn, $config['username'], $config['password'], array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
            $this->_connection->query("SET NAMES utf8");
        }
        return $this->_connection;
    }

    /**
     * 数据库重新连接
     */
    public function reconnect()
    {
        if ($this->_connection) {
            $this->_connection = null;
        }
        $this->connect();
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
     */
    public function setQuery($query)
    {
        $this->_query = $query;       
        return $query;
    }

    /**
     * 获取db的query对象
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

    public function select($columns = array('*'))
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->getQuery()->field($columns);
        return $this;
    }

    /**
     * 添加where条件,and组合       
     */
    public function where($conditions)
    {
        $this->getQuery()->whereAnd($conditions);
        return $this;
    }

    /**
     * 添加where条件,Or (a and b)
     */
    public function whereOr($conditions)
    {
        $this->getQuery()->whereOr($conditions);
        return $this;
    }

    /**
     * in 条件拼装
     */
    public function whereIn($conditions)
    {
        $this->getQuery()->whereIn($conditions);
        return $this;
    }

    /**
     * not in 条件拼装
     */
    public function whereNotIn($conditions)
    {
        $this->getQuery()->whereNotIn($conditions);
        return $this;
    }

    /**
     * 获取一条数据
     */
    public function first()
    {
        $sql = $this->toSql();
        $stmt = $this->query($sql['sql'], $sql['bind']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * 获取多条数据
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
        return (int) $item[0];
    }

    /**
     * 获取结果集中某一个字段的列表
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
     */
    public function orderBy($column, $order = "DESC")
    {
        $this->getQuery()->orderBy(array(
            $column => $order
        ));
        return $this;
    }

    /**
     * 插入(单条/多条)数据(non-PHPdoc)
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
     * 替换插入数据
     */
    public function replace($data)
    {
        $sql = $this->getQuery()->insert($data, 'REPLACE');
        $this->query($sql, $this->getQuery()
            ->getBinding("insert"));
        return $this->_connection->lastInsertId();
    }

    /**
     * 删除数据
     */
    public function delete($conditions)
    {
        $sql = $this->getQuery()->delete($conditions);
        return $this->query($sql, $this->getQuery()
            ->getBinding("where"));
    }

    /**
     * 更新数据
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
}
