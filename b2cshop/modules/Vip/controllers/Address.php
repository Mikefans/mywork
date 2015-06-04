<?php
class AddressController extends Controllers_Abstract {
    
    
   public function listAction() {
       $addressModel = \Vip\AddressModel::getInstance();
       $result = $addressModel->getAddressList();
       $this->getView()->assign('adds',$result);
   }
   
   public function saveAction()
   {
       $this->disableView();
   	   $params = $this->getParams();
   	   $addressModel = \Vip\AddressModel::getInstance();
   	   $result = $addressModel->save($params);
   	   $this->response($result);
   }
   
   public function delAction()
   {
       $this->disableView();
       $params = $this->getParams();
       $addressModel = \Vip\AddressModel::getInstance();
       $result = $addressModel->del($params);
       $this->response($result);
   }
   
   public function newAction()
   {
       $this->disableView();
       $params = $this->getParams();
       $addressModel = \Vip\AddressModel::getInstance();
       $result = $addressModel->newAdd($params);
       $this->response($result);
   }
}
?>

