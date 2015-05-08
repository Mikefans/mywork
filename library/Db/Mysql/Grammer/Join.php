<?php
namespace Hlg\Db\Mysql\Grammer;

class Join
{

    protected $_joinGrammar;

    /**
     * 内连接
     *
     * @param unknown $table            
     * @param unknown $alias
     *            别名
     */
    function innerJoin($table, $alias)
    {
        $this->_joinGrammar = $this->_join("INNER", $table, $alias);
    }

    /**
     * 左连接
     *
     * @param unknown $table            
     * @param unknown $alias
     *            别名
     */
    function leftJoin($table, $alias)
    {
        $this->_joinGrammar = $this->_join("LEFT", $table, $alias);
    }

    /**
     * 添加on条件
     *
     * @param unknown $condition            
     */
    function on($condition)
    {
        if ($this->_joinGrammar) {
            $onGrammer = array();
            foreach ($condition as $key => $val) {
                if (is_array($val)) {
                    list ($operate, $value) = $val;
                } else {
                    list ($operate, $value) = array(
                        "=",
                        $val
                    );
                }
                $onGrammer[] = $key . $operate . $val;
            }
            $this->_joinGrammar .= " on " . join(" AND ", $onGrammer);
        }
    }

    /**
     * 获取join语法拼装结果
     */
    public function getGrammer()
    {
        return $this->_joinGrammar;
    }

    /**
     * 拼接join语法
     *
     * @param unknown $type            
     * @param unknown $table            
     * @param unknown $alias            
     * @return string
     */
    protected function _join($type, $table, $alias)
    {
        return $type . " JOIN " . $table . " as " . $alias;
    }
}
?>