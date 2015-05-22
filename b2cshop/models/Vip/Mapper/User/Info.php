<?php 
namespace Vip\Mapper\User;

class InfoModel extends \Mapper\Abstracts
{
    
    protected $_name = 'vip_user_info';
    
    protected $_primaryKey ='uid';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function login($params)
    {
        $name = $params['user_name'];
        $pwd = $params['pass_word'];
    	$result = $this->where(array(
    		'user_name' => $name
    	))
    	   ->first();
    	if (empty($result)){
    	    \Core::setError('用户名或密码错误');
    	}
    	$pwds = $result['user_passwd'];
    	if ($pwds != md5($pwd)){
    		\Core::setError('用户名或密码错误');
    	}
    	$session = \Yaf\Session::getInstance();
    	$session->m_user = $result;
    	return array('msg' => 'success');
    }
    
    public function register($params)
    {
        $this->_verifyParams($params);
        $this->_verifyUnique($params);
        $data = array(
        	'user_name' => $params['user_name'],
            'user_email' => $params['email'],
            'user_passwd' => md5($params['pass_word']),
            'user_mobile' => $params['mobile'],
            'create_time' => date('Y-m-d H:i:s'),
            'is_delete' => 0
        );
        $uid = $this->insert($data);
        return array('uid' => $uid);
    }
    
    /**
     * 验证姓名邮箱的合法性
     * @param unknown $params
     */
    protected function _verifyParams($params)
    {
        if (!preg_match("/^[^_][\x{4e00}-\x{9fa5}a-zA-Z0-9_]{3,15}$/u", $params['user_name'])){
            \Core::setError('用户名只允许中文，字母，数字以及下划线');
        }
        if (!preg_match("/^[a-z0-9]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,4}$/i", $params['email'])){
            \Core::setError('邮箱格式不正确');
        }
        if (!preg_match("/^1[358]{1}[0-9][9]$/", $params['mobile'])){
            \Core::setError('手机号码格式不正确');
        }
    }
    
    /**
     * 验证唯一性
     * @param unknown $params
     */
    protected function _verifyUnique($params)
    {
    	$result = $this->where(array(
    		'user_name' => $params['user_name']
    	))
    	       ->first();
    	if (!empty($result)){
    	    \Core::setError('用户名已存在');
    	}
    	$result = $this->where(array(
    	      	'user_email' => $params['email'],
    	      ))
    	      ->first();
        if (!empty($result)){
          \Core::setError('邮箱已存在');
        }
    }
}


?>