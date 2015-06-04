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
    	    'order_pay' => $pay,
    	    'create_time' => date('Y-m-d H:i:s')
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
        $status = isset($params['status']) ? (int)$params['status'] : '';
    	$pageNum = isset($params['page_num']) ? (int)$params['page_num'] : 1;
    	$pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 5;
    	
    	if (!empty($status)){
    	    if ($status == -1){
    	    	$status = 0;
    	    }
    		$this->where(array(
    			'order_status' => $status
    		));
    	}
    	$result = $this->where(array(
    		'vip_delete' => 0
    	))
    	       ->skip(($pageNum-1) * $pageSize)
    	       ->limit($pageSize)
    	       ->orderBy('modified_time','desc')
    	       ->get();
    	$totalRecords = $this->count();
    	if (empty($result)){
    	    return array(
    		'data' => array(),
    	    'page_count' => 0,
    	    'page_num' => 1
    	);
    	}
    	foreach ($result as $key => $res){
    		$itemIds[] = $res['item_id'];
    	}
    	$itemIds = array_unique($itemIds);
    	$itemModel = \Item\ItemModel::getInstance();
    	$itemResult = $itemModel->whereIn(array(
    		'item_id' => $itemIds
    	))
    	       ->get();
    	foreach ($result as $key => $res){
    		foreach ($itemResult as $ir){
    			if ($ir['item_id'] == $res['item_id'] ){
    				$result[$key]['item_title'] = $ir['item_title'];
    				$result[$key]['item_img'] = $ir['item_img'];
    			}
    		}
    	}
    	
    	return array(
    		'data' => $result,
    	    'page_count' => floor($totalRecords / $pageSize),
    	    'page_num' => $pageNum
    	);
    }
    
    public function alterOrder($params)
    {
        $type = $params['type'];
        $orderId = $params['order_id'];
        if (empty($orderId)){
            \Core::setError('参数错误');
        }
        $this->where(array(
            'order_id' => $orderId
        ));
        if ($type == 2 ){//收货
            $this->update(array('order_status' => 3));
        }elseif ($type == 4 ){ //删除
            $this->update(array('vip_delete' => 1));
        }elseif ($type == 3){//评价
            $this->update(array('order_status' => 4));
        }
        return array('msg' => 'ok');
    }
}


?>