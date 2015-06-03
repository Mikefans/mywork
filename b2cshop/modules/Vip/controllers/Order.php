<?php
class OrderController extends Controllers_Abstract {
    
   public function addAction(){
        $this->disableView();
        $params= $this->getParams();
        $session = \Yaf\Session::getInstance();
        $session->order = $params;
        $this->response(array('msg'=>'ok'));
   }
   
   public function createAction()
   {
       $this->disableView();
       $params= $this->getParams();
       $orderMapper = \Vip\OrderModel::getInstance();
       $result = $orderMapper->createOrder($params);
       $this->response($result);
   }
   
   public function payAction()
   {
   	    $params = $this->getParams();
   	    $orderMapper = \Vip\OrderModel::getInstance();
   	    $order = $orderMapper->getOrderDetail($params);
   	    $this->getView()->assign('order',$order);
   }
   
   public function orderAction()
   {
   	    $session = \Yaf\Session::getInstance();
   	    $order = $session->order;
   	    $addressMapper = \Vip\AddressModel::getInstance();
   	    $result = $addressMapper->getAddressList();
   	    $itemMapper = \Item\ItemModel::getInstance();
   	    $item = $itemMapper->getItemDetail($order);
   	    $this->getView()->assign('order',$order);
   	    $this->getView()->assign('address',$result);
   	    $this->getView()->assign('item',$item);
   }
   
   public function moneyAction()
   {
       $this->disableView();
       $params = $this->getParams();
       $orderMapper = \Vip\OrderModel::getInstance();
   	   $result = $orderMapper->payMoney($params);
   	   $this->response($result);
   }
   
   public function listAction()
   {
       $params = $this->getParams();
       $orderMapper = \Vip\OrderModel::getInstance();
       $result = $orderMapper->getList($params);
       $this->getView()->assign('list',$result);
   }
}
?>

