<?php
namespace Hlg\Cache;

class Memcache
{

    protected $_mem;

    /**
     * 链接memcache
     */
    public function connect()
    {
        $memAddr = explode(':', \Yaf\Registry::get("appConfig")->application->cache->memcache);
        $this->_mem = new \Memcache();
        $weight = isset($memAddr[2]) ? (int) $memAddr[2] : 2;
        $this->_mem->addServer($memAddr[0], $memAddr[1], true, 1, $weight, 15, true);
    }

    /**
     * 获取key的值
     * 
     * @param unknown $key            
     */
    public function get($key)
    {
        $retry = 3;
        $v = '';
        while ($retry) {
            $retry --;
            try {
                $v = $this->_mem->get($key);
                $retry = 0;
            } catch (\Exception $e) {
                if ($retry) {
                    usleep(200000); // 0.2s
                } else {
                    throw $e;
                }
            }
        }
        return $v;
    }

    /**
     * 设置cache值
     * 
     * @param unknown $key            
     * @param unknown $value            
     * @param number $expire            
     */
    public function set($key, $value, $expire = 0)
    {
        return $this->_mem->set($key, $value, false, $expire);
    }

    public function add($key, $value, $expire = 0)
    {
        return $this->_mem->add($key, $value, false, $expire);
    }

    /**
     * 删除缓存key值
     * 
     * @param unknown $key            
     * @throws Exception
     */
    public function delete($key)
    {
        return $this->_mem->delete($key);
    }

    /**
     * 递增$key的值
     * 
     * @param unknown $key            
     * @param number $offset            
     */
    public function increment($key, $offset = 1)
    {
        return $this->_mem->increment($key, $offset);
    }

    /**
     * 减少$key的值
     * 
     * @param unknown $key            
     * @param number $offset            
     */
    public function decrement($key, $offset = 1)
    {
        return $this->_mem->decrement($key, $offset);
    }
}
?>