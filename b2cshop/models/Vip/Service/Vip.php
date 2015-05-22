<?php
namespace Vip\Service; 

class VipModel
{
	public function login($params)
	{
		$mapper = \Vip\Mapper\User\InfoModel::getInstance();
		$result = $mapper->login($params);
		return $result;
	}
	
	
	
	public function checkRegister($params){
		
	}
}


?>