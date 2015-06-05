<?php
class UserController extends Controllers_Abstract {
    
   public function infoAction(){
       //$this->disableView();
       $session = \Yaf\Session::getInstance();
       $user = $session->m_user;
       $this->getView()->assign('user',$user);
   }
   
   public function  alterAction()
   {
        $this->disableView();
        $params = $this->getParams();
       	$userModel = \Vip\UserModel::getInstance();
       	$result = $userModel->alterUser($params);
       	$this->response($result);
   }
   
   public function purseAction()
   {
   	
   }
   
   public function inmoneyAction()
   {
       $this->disableView();
   	    $params = $this->getParams();
   	    $userModel = \Vip\UserModel::getInstance();
   	    $result = $userModel->inMoney($params);
   	    $this->response($result);
   }
   
   public function outmoneyAction()
   {
       $this->disableView();
       $params = $this->getParams();
       $userModel = \Vip\UserModel::getInstance();
       $result = $userModel->outMoney($params);
       $this->response($result);
   }
}
?>

