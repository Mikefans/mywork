<?php
class IndexController extends Controllers_Abstract {
    
    
   public function indexAction() 
   {
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

