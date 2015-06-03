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
    	       ->limit(5)
    	       ->get();
    	return $result;
    }
  
}


?>