<?php
class PublicController extends Controllers_Abstract {
    
    
   public function loginAction() 
   {
       $this->getView()->assign("content", "Hello World");
   }
   
   public function logoutAction(){
        $this->disableView();
   	    $session = \Yaf\Session::getInstance();
   	    $session->del('m_user');
   }
   
   public function loginsAction(){
        $this->disableView();
        $params = $this->getParams();
        $session = \Yaf\Session::getInstance();
        $code = $session->login_code;
        if ($code != $params['verify_code']){
            \Core::setError('验证码错误');
        }
		$mapper = \Vip\Mapper\User\InfoModel::getInstance();
		$result = $mapper->login($params);
		$this->response($result);
   }
   
   public function registerAction(){
       $this->disableView();
       $params = $this->getParams();
	   $mapper = \Vip\Mapper\User\InfoModel::getInstance();
	   $result = $mapper->register($params);
       $this->response($result);
   }
}
?>

