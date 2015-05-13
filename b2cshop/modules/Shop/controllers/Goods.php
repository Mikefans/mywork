<?php
class GoodsController extends Controllers_Abstract {
    
    
   public function indexAction() 
   {
       $this->getView()->assign("content", "Hello World");
   }
   
   
   public function goodsDetailAction(){
        $params= $this->getParams();
//         $service = new \Vip\Service\VipModel();
//         $result = $service->login($params);
//         var_dump($result);die;
   }
   
   public function addToCarAction()
   {
   	
   }
   
   public function collectAction()
   {
   	
   }
   
   public function cacleCollectAction()
   {
   	
   }
   
   public function buyGoodsAction()
   {
   	
   }
   
   public function commitOrderAction()
   {
   	
   }
   
   public function gotoPayAction()
   {
   	
   }
   
   public function payAction()
   {
   	
   }
   
   public function showCarAction()
   {
   	
   }
   
   public function showCollectAction()
   {
   	
   }
   
   public function getOrderAction()
   {
   	
   }
   
   
   
}
?>

