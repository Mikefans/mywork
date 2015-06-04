<?php 
namespace Vip;

class AddressModel extends \Mapper\Abstracts
{
    
    protected $_name = 'vip_address';
    
    protected $_primaryKey ='address_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function getAddressList()
    {
        $user = \Core::getUser();
    	$result = $this->where(array(
    		'user_id' => $user['user_id'],
    	    'is_delete' => 0
    	))
    	       ->orderBy('create_time','DESC')
    	       ->skip(0)
    	       ->limit(20)
    	       ->get();
    	return $result;
    }
    
    public function save($params)
    {
        unset($params['vip/address/save']);
    	$data = $params;
    	$this->where(array(
    		'address_id' => $params['address_id']
    	))
    	       ->update($data);
    	return array('id' => $params['address_id']);
    }
    
    public function del($params)
    {
        $this->where(array(
            'address_id' => $params['address_id']
        ))
             ->update(array('is_delete' => 1));
        return array('id' => $params['address_id']);
    }
    
    public function newAdd($params)
    {
        unset($params['vip/address/new']);
        $session = \Yaf\Session::getInstance();
        $user = $session->m_user;
        $data = $params;
        $data['user_id'] = $user['user_id'];
        $id = $this->insert($data);
        return array('id' => $id );
    }
}



?>