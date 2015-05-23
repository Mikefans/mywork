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
	    header("location:/index/setError");
// 	     $result = (object)array(
//             'status' => 'error',
//             'desc' => $msg
//         );
//         $result = json_encode($result, JSON_UNESCAPED_UNICODE);
// 	    echo $result;die;
	}
}


?>