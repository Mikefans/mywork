<?php 
class Core
{

	public function getUser()
	{
		$session = \Yaf\Session::getInstance();
		$user = $session->m_user;
		return $user;
	}
	
	public function setError($msg){
	    header("location:/index/error?msg=$msg");exit();
	}
}


?>