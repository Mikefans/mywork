<?php
class LoginController extends Controllers_Abstract {
    
    
   public function indexAction() {
       
      // $this->disableView();
      // $this->initView();
       //$this->getView()->display(APP_PATH. "/views/test.phtml");
       $view = new Tools\View();
       $view->render(APP_PATH. "/views/test1.phtml");
       $this->getView()->assign("content", "Hello World");
   }
   
   
   public function loginAction(){
   //	echo 'dwa';die;
   //$this->getView()->disableView();
        $params= $this->getParams();
        $service = new \Vip\Service\VipModel();
        $result = $service->login($params);
        var_dump($result);die;
   }
}
?>

