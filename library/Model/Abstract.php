<?php
/**
 * 数据model层父抽象类
 * @author yuyang
 *
 */
namespace Hlg;

/**
 * 数据模型的抽象类, 普通的数据模型都要基于此类,
 */
abstract class Model_Abstract implements \JsonSerializable
{

    protected $_modifiedFields = array();

    protected $_data = array();

    public function __construct($options = null)
    {
        if ($options) {
            $this->setData($options);
        }
    }

    /**
     * 调用函数响应
     * 
     * @param unknown $method            
     */
    public function __call($func, $arguments)
    {
        if (preg_match('/^set([A-Z])([a-zA-Z0-9_]+)$/', $func, $match)) {
            $name = lcfirst($match[1]) . $match[2];
            $property = "_" . $name;
            if (! property_exists($this, $property)) {
                throw new \Exception('Invalid model property:' . $property);
            }
            $this->{$property} = $arguments[0];
            $columnName = $this->_transformColumn($name);
            $this->_modifiedFields[$columnName] = $arguments[0];
            return $this;
        }
        if (preg_match('/^get([A-Z])([a-zA-Z0-9_]+)$/', $func, $match)) {
            $property = "_" . lcfirst($match[1]) . $match[2];
            if (! property_exists($this, $property)) {
                throw new \Exception('Invalid model property:' . $property);
            }
            return $this->{$property};
        }
        throw new \Exception('Invalid model method:' . $func);
    }

    /**
     *
     * @param string $name            
     * @param mixed $value            
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $method = "set" . ucfirst($name);
        return $this->{$method}($value);
    }

    /**
     *
     * @param string $name            
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        $method = "get" . ucfirst($name);
        return $this->{$method}();
    }

    /**
     * 判断属性是否存在魔术方法
     * 
     * @param unknown $name            
     */
    public function __isset($name)
    {
        $property = "_" . $name;
        return property_exists($this, $property);
    }

    /**
     * 通用设置方法
     *
     * @param array $options
     *            参数. 如果是类, 必需实现了toArray(), 或者Traversabl接口的类.
     * @return \Base\Model\AbstractModel
     */
    public function setData($options)
    {
        if (is_object($options)) {
            if (method_exists($options, 'toArray')) {
                $options = $options->toArray();
            } else 
                if (! ($options instanceof \Traversable)) {
                    return $this;
                }
        } else 
            if (! is_array($options)) {
                return $this;
            }
        foreach ($options as $key => $value) {
            $property = '_' . lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
        
        return $this;
    }

    /**
     * 通用获取数据方法
     * 
     * @param unknown $name            
     */
    public function getData($name)
    {
        $property = '_' . lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
        if (property_exists($this, $property)) {
            return $this->{$property};
        }
        return null;
    }

    /**
     * 在update的时候获取当前有更新的数据
     * 
     * @return multitype:
     */
    public function updateData()
    {
        $data = $this->toArray();
        return array_intersect_key($this->_modifiedFields, $data);
    }

    /**
     * 刷新当前脏数据
     */
    public function refresh()
    {
        $this->_modifiedFields = array();
    }

    /**
     * 将属性键值转换为数据库字段值如 shopNick转换为shop_nick
     * 
     * @param unknown $name            
     * @return mixed
     */
    protected function _transformColumn($name)
    {
        return strtolower(preg_replace("/(?!^)(?=[A-Z])/", "_", $name));
    }

    /**
     * 合并关联model 数组输出
     * 
     * @param unknown $modelName            
     * @return multitype:
     */
    public function mergeToArray($modelName)
    {
        $property = "_" . ucfirst($modelName) . "Model";
        if (property_exists($this, $property)) {
            $obj = $this->{$property};
            if (is_object($obj) && $obj instanceof \Hlg\Model_Abstract) {
                $arr = $obj->toArray();
                return array_merge($this->toArray(), $arr);
            }
        }
        return $this->toArray();
    }
    
    /**
     * 格式化json_encode返回(non-PHPdoc)
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(){
        return $this->toArray();
    }

    /**
     * 模型数据数组输出
     */
    abstract public function toArray();
}