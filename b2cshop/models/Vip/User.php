<?php 
namespace Vip;

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
        if (!preg_match("/^[^_][\x{4e00}-\x{9fa5}a-zA-Z0-9_]{1,15}$/u", $params['user_name'])){
            \Core::setError('用户名只允许中文，字母，数字以及下划线');
        }
        if (!preg_match("/^[a-z0-9]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,4}$/i", $params['email'])){
            \Core::setError('邮箱格式不正确');
        }
        if (!preg_match("/^1[358]{1}[0-9]{9}$/", $params['mobile'])){
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
        $result = $this->where(array(
            'user_mobile' => $params['mobile'],
        ))
        ->first();
        if (!empty($result)){
            \Core::setError('手机已存在');
        }
    }
    
    public function alterUser($params)
    {
        $session = \Yaf\Session::getInstance();
        $user = $session->m_user;
        unset($params['vip/user/alter']);
        $data = $params;
        if (empty($params['user_passwd'])){
        	$data['user_passwd'] = $user['user_passwd'];
        }else{
        	$data['user_passwd'] = md5($params['user_passwd']);
        }
    	$this->where(array(
    		'user_id' => $user['user_id']
    	))
    	       ->update($data);
    	$result = $this->where(array(
    		'user_id' => $user['user_id']
    	))
    	   ->first();
    	$session->m_user = $result;
    	return array('msg' => 'ok');
    }
    
    public function inMoney($params)
    {
    	$session = \Yaf\Session::getInstance();
    	$user = $session->m_user;
    	$money = $params['in_money'] + $user['user_money'];
    	$this->where(array(
    		'user_id' => $user['user_id']
    	))
    	       ->update(array('user_money' => $money));
    	$user['user_money'] = $money;
    	$session->m_user = $user;
    	return array('money' => $money);
    }
    
    public function outMoney($params)
    {
        $session = \Yaf\Session::getInstance();
        $user = $session->m_user;
        if ($params['out_money'] > $user['user_money']){
        	\Core::setError('余额不足哦亲！');
        } 
    	$cashModel  = \Vip\CashModel::getInstance();
    	$cashModel->insert(array(
    		'user_id' => $user['user_id'],
    	    'cash_num' => $params['out_money'],
    	));
    	$user['user_money'] -= $params['out_money']; 
    	$session->m_user = $user;
    	return array('money'=>$user['user_money']);
    }
}


?>