<?php

namespace Hlg\Model;

class Builder
{
    /**
     * 解析配置生成model文件
     * 
     * @param unknown $config            
     */
    public function run($buildConfig)
    {
        $db = \Hlg\Db::connection($buildConfig['module']);
        $namespacePrefix = isset($buildConfig['namespace_prefix'])?$buildConfig['namespace_prefix']:null;
        if (isset($buildConfig['table'])) {
            $tables = explode(",", $buildConfig['table']);
        } else {
            $tables = $db->showTables();
        }
        foreach ($tables as $table) {
            if (is_array($table)) {
                $table = current($table);
            }
            if (preg_match("#(?=\d{4}$)#", $table)) {
                // 分表的表略过
                continue;
            }
            $this->_run($db, $table, $namespacePrefix);
        }
    }

    /**
     * 解析table生成model文件
     * 
     * @param unknown $db            
     * @param unknown $table            
     */
    protected function _run($db, $table, $namespacePrefix = null)
    {
        $db->table($table);
        $tableInfo = $db->tableInfo();
        if(!$tableInfo) return;
        $namespaces = $this->_getNamespaces($table);
        if($namespacePrefix){
            array_unshift($namespaces, ucfirst($namespacePrefix));
        }
        $modelDir = $this->_createModelDir($namespaces);
        $namespace = implode("\\", array_slice($namespaces, 0, count($namespaces) - 1));
        $className = $namespaces[count($namespaces) - 1];
        $columns = $db->showColumns();
        $columnFormat = $this->_getColumnFormat();
        $modelFileFormat = $this->_getModelFileFormat();
        $arrFormat = $this->_getArrFormat();
        $columnData = $arrData = "";
        foreach ($columns as $column) {
            $property = $this->_transformProperty($column['COLUMN_NAME']);
            $arrTxt = sprintf($arrFormat, $column['COLUMN_NAME'], $property);
            $arrData .= $arrTxt . "\n";
            $columnTxt = sprintf($columnFormat, $column['COLUMN_COMMENT'], $column['COLUMN_TYPE'], $property);
            $columnData .= $columnTxt . "\n";
        }
        $modelData = sprintf($modelFileFormat, $tableInfo['TABLE_COMMENT'],$namespace, $className, $columnData, $arrData);
        file_put_contents($modelDir . $className . ".php", $modelData);
        printf("create model file:%s\n", $modelDir . $className . ".php");
    }

    /**
     * 转换成对象属性样式shop_nick->shopNick
     * 
     * @param unknown $columnName            
     * @return string
     */
    protected function _transformProperty($columnName)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $columnName))));
    }

    /**
     * 获取命名空间数组
     * 
     * @param unknown $table            
     * @return multitype: string
     */
    protected function _getNamespaces($table)
    {
        return array_map(function ($namespace)
        {
            return ucfirst($namespace);
        }, explode("_", $table));
    }

    /**
     * 创建模块目录
     * 
     * @param unknown $dir            
     */
    protected function _createModelDir($namespace)
    {
        $dirPrefix = APP_PATH . DS . "models" . DS;
        foreach ($namespace as $idx => $dir) {
            if ($idx != count($namespace) - 1) {
                if (! is_dir($dirPrefix . $dir)) {
                    mkdir($dirPrefix . $dir);
                }
                $dirPrefix .= $dir . DS;
            }
        }
        return $dirPrefix;
    }

    protected function _getModelFileFormat()
    {
        $fileFormat = <<<EOT
<?php
/**
 * %s模型
 * @author 
 *
 */
namespace %s;
class %sModel extends \Hlg\Model_Abstract{
%s
    public function toArray(){
        return array(
%s
        );
    }
}
?>
EOT;
        return $fileFormat;
    }

    protected function _getColumnFormat()
    {
        $format = <<<EOT
    /**
     * %s
     * @var %s
     */
    protected \$_%s;
        
EOT;
        return $format;
    }

    /**
     * 获取返回数组的toArray的样式
     */
    protected function _getArrFormat()
    {
        $format = <<<EOT
            '%s' => \$this->_%s,
EOT;
        return $format;
    }
}
?>