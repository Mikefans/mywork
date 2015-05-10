<?php 
namespace Vip\Mapper\User;

class InfoModel extends Mapper_abstract
{
    
    protected $_name = 'vip_user_info';
    
    protected $_primaryKey ='uid';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function login($params)
    {
    	$result = $this->where(array(
    		'uid' => $params['uid']
    	))
    	   ->get();
    	var_dump($result);die;
    }
}


?>