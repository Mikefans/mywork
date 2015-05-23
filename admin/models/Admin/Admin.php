<?php 
namespace Admin;

class AdminModel extends \Mapper\Abstracts
{
    
    protected $_name = 'admin_admin';
    
    protected $_primaryKey ='admin_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function login($params)
    {
        $session = \Yaf\Session::getInstance();
        $name = $params['user_name'];
        $pwd = $params['pass_word'];
    	$result = $this->where(array(
    		'admin_name' => $name,
    	    'is_delete' => 0
    	))
    	   ->first();
    	if (empty($result)){
    	    $session->del('login_code');
    	    \Core::setError('用户名或密码错误1');
    	}
    	$pwds = $result['admin_passwd'];
    	if ($pwds != md5($pwd)){
    	    $session->del('login_code');
    		\Core::setError('用户名或密码错误11');
    	}
    	$session = \Yaf\Session::getInstance();
    	$session->m_admin = $result;
    	return array('msg' => 'success');
    }
}


?>