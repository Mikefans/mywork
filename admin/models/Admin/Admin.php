<?php 
namespace Vip\Mapper;

class UserModel extends \Mapper\Abstracts
{
    
    protected $_name = 'vip_user';
    
    protected $_primaryKey ='user_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function login($params)
    {
        $name = $params['admin_name'];
        $pwd = $params['pass_word'];
    	$result = $this->where(array(
    		'admin_name' => $name,
    	    'is_delete' => 0
    	))
    	   ->first();
    	if (empty($result)){
    	    \Core::setError('用户名或密码错误');
    	}
    	$pwds = $result['admin_passwd'];
    	if ($pwds != md5($pwd)){
    		\Core::setError('用户名或密码错误');
    	}
    	$session = \Yaf\Session::getInstance();
    	$session->m_admin = $result;
    	return array('msg' => 'success');
    }
}


?>