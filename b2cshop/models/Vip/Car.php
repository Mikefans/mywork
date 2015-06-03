<?php 
namespace Vip;

class CarModel extends \Mapper\Abstracts
{
    
    protected $_name = 'vip_car';
    
    protected $_primaryKey ='car_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function addToCar($params){
    	unset($params['vip/car/addTocar']);
    	$user = \Core::getUser();
    	$data = $params;
    	$data['user_id'] = $user['user_id'];
    	$id = $this->insert($data);
    	return  array('id' => $id);
    }
  
}


?>