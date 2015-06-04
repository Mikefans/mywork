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
    
    public function getOrderList($params)
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
    		'admin_delete' => 0
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
    	if ($type == 1){
    		$this->update(array('order_status' => 5));
    	}elseif ($type == 3 ){
    	    $this->update(array('admin_delete' => 1));
    	}elseif ($type == 2){
    		$orderAddNum = $params['order_add_num'];
    		$orderAdd = $params['order_add'];
    		$this->update(array(
    			'order_add_num' => $orderAddNum,
    		    'order_add_company' => $orderAdd,
    		    'order_status' => 2
    		));
    	}
    	return array('msg' => 'ok');
    }
}


?>