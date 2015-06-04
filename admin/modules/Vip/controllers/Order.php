<?php
class OrderController extends Controllers_Abstract {
    
    /**
     * 订单列表
     * status 0 未付款  1 待发货  2 待确认收货  3 待评价 4 已评价
     */
    public function listAction() 
    {
        $params = $this->getParams();
        $orderModel = \Vip\OrderModel::getInstance();
        $result = $orderModel->getOrderList($params);
        $this->getView()->assign('orders',$result['data']);
        $this->getView()->assign('pageCount',$result['page_count']);
        $this->getView()->assign('pageNum',$result['page_num']);
    }
    
    public function alterAction()
    {
        $this->disableView();
    	$params = $this->getParams();
    	$orderModel = \Vip\OrderModel::getInstance();
        $result = $orderModel->alterOrder($params);
        $this->response($result);
    }
}
?>

