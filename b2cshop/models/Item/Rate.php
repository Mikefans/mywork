<?php 
namespace Item;

class RateModel extends \Mapper\Abstracts
{
    
    protected $_name = 'item_rate';
    
    protected $_primaryKey ='rate_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }

    public function getItemRate($params){
    	$result = $this->where(array(
    		'item_id' => (int)$params['item_id'],
    	    'is_delete' => 0 
    	))
    	       ->orderBy('rate_id', 'DESC')
    	       ->skip(0)
    	       ->limit(50)
    	       ->get();
    	return $result;
    }
    
    //订单评价
    public function addRate($params){
        $user = \Yaf\Session::getInstance()->m_user;
        $orderModel = \Vip\OrderModel::getInstance();
        $order = $orderModel->where(array(
        	'order_id'=> $params['order_id']
        ))
                ->first();
        if (empty($order)){
        	\Core::setError('订单不存在');
        }
        $orderModel->where(array(//设置订单状态
            'order_id' => $params['order_id']
        ))
            ->update(array('order_status' => 4));
        $itemId = $order['item_id'];
        
        $data = array(
        	'item_id' => $itemId,
            'user_id' => $user['user_id'],
            'rate_content' => $params['content'],
            'user_name' => $user['user_name'],
            'create_time' => date('Y-m-d H:i:s')
        );
        $id = $this->insert($data);
        return array('id' => $id);
    }
    
    //获取我的评价记录
    public function rateList($params)
    {
        $user = \Yaf\Session::getInstance()->m_user;
        $pageNum = isset($params['page_num']) ? (int)$params['page_num'] : 1;
        $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 10;
    	$result = $this->where(array(
    		'user_id' => $user['user_id']
    	))
    	       ->orderBy('create_time','DESC')
    	       ->skip(($pageNum - 1 )* $pageSize)
    	       ->limit($pageSize)
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
}


?>