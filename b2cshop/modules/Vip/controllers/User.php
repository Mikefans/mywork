<?php
class UserController extends Controllers_Abstract {
    
    
   public function indexAction() {
       
      // $this->disableView();
      // $this->initView();
       //$this->getView()->display(APP_PATH. "/views/test.phtml");
       $view = new Tools\View();
       $view->render(APP_PATH. "/views/test1.phtml");
       $this->getView()->assign("content", "Hello World");
   }
   
   
   public function infoAction(){
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
}
?>

