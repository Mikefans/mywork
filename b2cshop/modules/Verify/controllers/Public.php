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
        if (empty($code)){
            \Core::setError('请刷新验证码');
        }
        if ($code != $params['verify_code']){
            $session->del('login_code');
            \Core::setError('验证码错误');
        }
		$mapper = \Vip\Mapper\UserModel::getInstance();
		$result = $mapper->login($params);
		$this->response($result);
   }
   
   public function registerAction(){
       $this->disableView();
       $params = $this->getParams();
	   $mapper = \Vip\Mapper\UserModel::getInstance();
	   $result = $mapper->register($params);
       $this->response($result);
   }
}
?>

