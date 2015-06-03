<?php 
namespace Vip;

class OrderModel extends \Mapper\Abstracts
{
    
    protected $_name = 'vip_order';
    
    protected $_primaryKey ='order_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function createOrder($params){
    	unset($params['vip/order/create']);
    	$session = \Yaf\Session::getInstance();
    	$order =$session->order;
    	$itemMapper = \Item\ItemModel::getInstance();
    	$item = $item = $itemMapper->getItemDetail($order);
    	$addressMapper = \Vip\AddressModel::getInstance();
    	$address = $addressMapper->where(array(
    		'address_id' => $params['address_id']
    	))
    	       ->first();
    	$user = \Core::getUser();
    	if ($item['item_promo_price'] != 0){
    		$pay = $item['item_promo_price'] + $item['item_post'];
    	}else {
    		$pay = $item['item_price'] + $item['item_post'];
    	}
    	$pay = 
    	$data =array(
    		'item_id' => $order['item_id'],
    	    'user_id' => $user['user_id'],
    	    'receiver_name' => $address['receiver_name'],
    	    'receiver_mobile' => $address['receiver_mobile'],
    	    'receiver_address' => $address['address_detail'],
    	    'order_pay' => $pay
    	); 
    	$id = $this->insert($data);
    	return  array('id' => $id);
    }
  
    public function  getOrderDetail($params)
    {
    	$result = $this->where(array(
    		'order_id' => $params['id']
    	))
    	       ->first();
    	return $result;
    }
    
    public function payMoney($params)
    {
    	$this->where(array(
    		'order_id' => $params['order_id']
    	))
    	   ->update(array(
    	   	'order_status' => 1
    	   ));
    	   return array('msg' => 'success');
    }
    
    public function getList($params)
    {
        $pageNum = isset($params['page_num']) ? (int)$params['page_num'] : 1;
        $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 1;
        $userId = \Core::getUser()['user_id'];
    	$result = $this->where(array(
    		'user_id' => $userId,
    	    'is_delete' => 0
    	))
    	       ->orderBy('create_time','desc')
    	       ->skip(($pageNum -1 ) * $pageSize)
    	       ->limit($pageSize)
    	       ->get(); 
    	return $result;
    }
}


?>