<?php
class PublicController extends Controllers_Abstract {
    
    
   public function loginAction() 
   {
       $this->getView()->assign("content", "Hello World");
   }
   
   
   public function loginsAction(){
   //	echo 'dwa';die;
   //$this->getView()->disableView();
        $params= $this->getParams();
        $service = new \Vip\Service\VipModel();
        $result = $service->login($params);
        var_dump($result);die;
   }
   
}
?>

