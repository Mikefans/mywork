<?php
/*
 * sql拼装类 @yuyang
 */
namespace Hlg\Db\Mysql;

class Query
{

    public $filter;

    public $source;

    public $distinct = false;

    public $where = '';

    public $orderBy = NULL;

    public $groupBy = NULL;

    public $limit = NULL;

    public $offset = NULL;

    public $fields = NULL;

    public $joinConditions = array();

    /**
     * The current query value bindings.
     *
     * @var array
     */
    protected $bindings = array(
        'join' => [],
        'where' => [],
        'having' => [],
        'order' => [],
        'update' => [],
        'delete' => [],
        'insert' => []
    );

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = array(
        '=',
        '<',
        '>',
        '<=',
        '>=',
        '<>',
        '!=',
        'like',
        'not like',
        'between',
        'ilike',
        '&',
        '|',
        '^',
        '<<',
        '>>',
        'rlike',
        'regexp',
        'not regexp',
        'inc'
    );

    public function source($source)
    {
        $this->source = $source;
    }

    public function distinct()
    {
        $this->distinct = true;
    }

    public function field($fields)
    {
        if (is_array($fields)) {
            $this->fields = join(",", $fields);
        }
        return $this;
    }

    public function whereAnd($conditions)
    {
        return $this->where($conditions, 'and');
    }

    public function whereOr($conditions)
    {
        $bind = false;
        if (is_array($conditions) && count($conditions) > 1) {
            $bind = true;
        }
        return $this->where($conditions, 'or', $bind);
    }

    public function whereIn($conditions, $type = "and")
    {
        $formatData = array();
        foreach ($conditions as $key => $val) {
            $formatData[] = $this->operator("in", $key, $val);
        }
        $where = join(' and ', $formatData);
        $bind = false;
        if (($type == 'or') && count($conditions) > 1) {
            $bind = true;
        }
        $this->_joinWhere($where, $type, $bind);
        return $this;
    }
    
    public function whereNotIn($conditions, $type = "and")
    {
        $formatData = array();
        foreach ($conditions as $key => $val) {
            $formatData[] = $this->operator("not in", $key, $val);
        }
        $where = join(' and ', $formatData);
        $bind = false;
        if (($type == 'or') && count($conditions) > 1) {
            $bind = true;
        }
        $this->_joinWhere($where, $type, $bind);
        return $this;
    }

    /**
     * where语句拼装
     *
     * @param unknown $conditions            
     * @param string $type            
     * @param string $bind            
     * @return \Hlg\Db\Mysql\Query
     */
    public function where($conditions, $type = 'and', $bind = false)
    {
        if (is_callable($conditions)) {
            // 绑定新的query语句组合(匿名函数实现)
            $conditions = $this->_bindQuery($conditions);
            $bind = true;
        }
        $where = $this->_compileWhere($conditions, ' and ');
        $this->_joinWhere($where, $type, $bind);
        return $this;
    }

    /**
     * left join 拼装
     * 
     * @param unknown $table            
     * @param unknown $alias            
     * @param unknown $pKey
     *            主表关联主键
     * @param unknown $fKey
     *            关联外间
     */
    public function leftJoin($table, $alias, $condition)
    {
        $joinGrammer = new Grammer\Join();
        $joinGrammer->leftJoin($table, $alias);
        $joinGrammer->on($condition);
        $this->joinConditions[] = $joinGrammer->getGrammer();
    }

    /**
     * inner join 拼装
     * 
     * @param unknown $table            
     * @param unknown $alias            
     * @param unknown $pKey
     *            主表关联主键
     * @param unknown $fKey
     *            关联外间
     */
    public function innerJoin($table, $alias, $condition)
    {
        $joinGrammer = new Grammer\Join();
        $joinGrammer->innerJoin($table, $alias);
        $joinGrammer->on($condition);
        $this->joinConditions[] = $joinGrammer->getGrammer();
    }

    public function filter($val, $like = false)
    {
        $filter = $this->filter;
        if (is_array($val)) {
            return $val = array_map($filter, $val);
        }
        return $filter($val, $like);
    }

    public function orderBy($order)
    {
        if (is_array($order)) {
            foreach ($order as $field => $orderBy) {
                $this->orderBy = (null === $this->orderBy) ? "{$field} ${orderBy}" : $this->orderBy . ", {$field} ${orderBy}";
            }
        }
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBy = $column;
    }

    public function skip($count)
    {
        $this->offset = (int) $count;
        return $this;
    }

    public function limit($count)
    {
        $this->limit = (int) $count;
        return $this;
    }

    /**
     * 绑定新的query对象
     *
     * @param unknown $bindFunc            
     * @return string
     */
    private function _bindQuery($bindFunc)
    {
        $newQuery = new self();
        call_user_func($bindFunc, $newQuery);
        $this->addBinding($newQuery->getBinding("where"), "where");
        return $newQuery->where;
    }

    private function _joinWhere($where, $type, $bind = false)
    {
        if ($bind) {
            $where = "(" . $where . ")";
        }
        if (! $this->where) {
            $this->where = $where;
        } else {
            $this->where .= ' ' . $type . ' ' . $where;
        }
    }

    private function _compileWhere($data, $salt = ',')
    {
        $formatData = array();
        if (is_string($data)) {
            $formatData[] = $data;
        } else {
            foreach ($data as $key => $val) {
                if (is_integer($key)) {
                    $formatData[] = $val;
                    continue;
                }
                if (is_array($val)) {
                    if (count($val) != 2) {
                        throw new \Exception("Value must be provided.");
                    }
                    list ($operator, $value) = $val;
                } else {
                    list ($operator, $value) = array(
                        "=",
                        $val
                    );
                }
                if ($this->invalidOperatorAndValue($operator, $value)) {
                    throw new \Exception("Value must be provided.");
                }
                $formatData[] = $this->operator($operator, $key, $value);
            }
        }
        return join($salt, $formatData);
    }

    public function operator($operator, $field, $val)
    {
        if ($operator == 'inc') {
            $val = ($val < 0) ? '-' . abs($val) : '+' . abs($val);
            return $field . "=" . $field . $val;
        }
        $like = ($operator == 'like' || $operator == 'not like') ? true : false;
        $operator = " ".$operator." ";
        if (is_array($val)) {
            $val = join(',', $this->filter($val));
            $val = "(" . $val . ")";
        } else {
            if (is_string($val) || ! is_numeric($val)) {
                $this->addBinding($val, "where");
                $val = "?";
            }
        }
        return $field . $operator . $val;
    }

    public function select()
    {
        if (empty($this->source)) {
            throw new \Exception("please init select table");
        }
        $source = $this->joinConditions ? "`" . $this->source . "` as main_table" : "`" . $this->source . "`";
        $fields = $this->fields ? "{$this->fields}" : '*';
        if ($this->limit) {
            // 分页获取数据的时候加上统计的标示
            $fields = "SQL_CALC_FOUND_ROWS " . $fields;
        }
        $this->fields = NULL;
        if ($this->distinct) {
            $query = "SELECT DISTINCT {$fields} from {$source}";
            $this->distinct = false;
        } else {
            $query = "SELECT {$fields} from {$source}";
        }
        if (! empty($this->joinConditions)) {
            $query .= " " . implode(" ", $this->joinConditions);
            $this->joinConditions = array();
        }
        if (! empty($this->where)) {
            $query .= " WHERE {$this->where}";
        }
        if (! empty($this->groupBy)) {
            $query .= " GROUP BY " . $this->groupBy;
            $this->groupBy = NULL;
        }
        if (! empty($this->orderBy)) {
            $query .= " ORDER BY {$this->orderBy}";
            $this->orderBy = NULL;
        }
        if (! empty($this->limit)) {
            if (! is_null($this->offset)) {
                $query .= " LIMIT {$this->offset},{$this->limit}";
                $this->offset = NULL;
            } else {
                $query .= " LIMIT {$this->limit}";
            }
            $this->limit = NULL;
        }
        return $query;
    }

    /**
     * 插入一条或多条新数据
     *
     * @param unknown $data            
     * @param unknown $type            
     * @return Ambigous <string, multitype:>|string
     */
    function insert($data, $type)
    {
        if (isset($data[0]) && is_array($data[0])) {
            return $this->batchInsert($data, $type);
        }
        $cols = $vals = array();
        foreach ($data as $col => $val) {
            if (is_null($val))
                continue;
            $cols[] = '`' . $col . '`';
            $vals[] = "?";
            if (is_int($val)) {
                $val = (string) $val;
            }
            $this->addBinding($val, "insert");
        }
        return "{$type} INTO {$this->source} (" . implode(', ', $cols) . ") " . "VALUES (" . implode(', ', $vals) . ")";
    }

    /**
     * 批量插入多条新数据
     *
     * @param unknown $data            
     * @param unknown $type            
     * @return multitype: string
     */
    function batchInsert($data, $type)
    {
        $object = $this;
        foreach ($data as $key => $val) {
            if ($key == 0) {
                $cols = array_keys($val);
            }
            $val = array_map(function ($value) use($object)
            {
                return $object->filter($value);
            }, $val);
            $vals[] = "(" . implode(', ', $val) . ")";
        }
        return "{$type} INTO {$this->source} (" . implode(', ', $cols) . ") " . "VALUES " . implode(', ', $vals);
    }

    /**
     * 更新数据
     *
     * @param unknown $data            
     * @param unknown $conditions            
     * @throws \Exception
     * @return string
     */
    function update($data, $conditions)
    {
        foreach ($data as $field => $val) {
            if (is_array($val)) {
                list ($operator, $value) = $val;
                $set[] = $this->operator($operator, $field, $value);
            } else {
                $set[] = "`{$field}` = ?";
                $this->addBinding($val, "update");
            }
        }
        if ($conditions)
            $this->where($conditions, "and");
        if (empty($this->where)) {
            throw new \Exception("UPDATE_OR_DELETE_WHERE_IS_EMPTY");
        }
        $sql = "UPDATE {$this->source} SET " . implode(', ', $set) . " WHERE {$this->where}";
        if(! empty($this->limit)){
            $sql .= " limit ".$this->limit;
            $this->limit = null;
        }
        return $sql;
    }

    function delete($conditions)
    {
        if ($conditions)
            $this->where($conditions, "and");
        if (empty($this->where)) {
            throw new \Exception("UPDATE_OR_DELETE_WHERE_IS_EMPTY");
        }
        $sql = "DELETE FROM {$this->source} where {$this->where}";
        if(! empty($this->limit)){
            $sql .= " limit ".$this->limit;
            $this->limit = null;
        }
        return $sql;
    }

    /**
     * Add a binding to the query.
     *
     * @param mixed $value            
     * @param string $type            
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function addBinding($value, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new \Exception("Invalid binding type: {$type}.");
        }
        
        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }
        
        return $this;
    }

    /**
     * get Binding for query
     */
    public function getBinding($type)
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new \Exception("Invalid binding type: {$type}.");
        }
        
        return $this->bindings[$type];
    }

    /**
     * 清理临时变量
     */
    public function clear()
    {
        $this->where = NULL;
        foreach ($this->bindings as $type => $bindings) {
            if ($bindings) {
                $this->bindings[$type] = [];
            }
        }
    }

    /**
     * Determine if the given operator and value combination is legal.
     *
     * @param string $operator            
     * @param mixed $value            
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        $isOperator = in_array($operator, $this->operators);
        
        return ($isOperator && $operator != '=' && is_null($value));
    }
}
