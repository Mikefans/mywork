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
}


?>