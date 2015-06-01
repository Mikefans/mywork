<?php 
namespace Item;

class ItemModel extends \Mapper\Abstracts
{
    
    protected $_name = 'item_item';
    
    protected $_primaryKey ='item_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function addItems($params)
    {
    	$data = $params;
    	$data['create_time'] = date('Y-m-d H:i:s');
    	unset($data['item/item/adds']);
    	$id = $this->insert($data);
    	return array('item_id'=> $id);
    }
     
    public function itemList($params)
    {
    	$status = isset($params['status']) ? (int) $params['status'] : '1';
    	$pageNum = isset($params['page_num']) ? (int) $params['page_num'] : 1;
    	$pageSize = isset($params['page_size']) ? (int) $params['page_size'] : 1;
    	$cateNext = isset($params['cate_next']) ? (int)$params['cate_next'] : '';
    	$cateFather = isset($params['cate_father']) ? (int)$params['cate_father'] :'';
    	$q = isset($params['q']) ? $params['q'] :'';
    	if ($cateFather){
    		$this->where(array(
    			'cate_id' => $cateFather
    		));
    	}
    	if ($cateNext){
    		$this->where(array(
    			'cate_id_before' => $cateNext
    		));
    	}
    	if ($q){
    		$this->where(array(
    			'item_title' => array('like',"{$q}")
    		));
    	}
    	$result = $this->where(array(
    		'is_delete' => 0,
    	    'item_status' => $status
    	))
    	       ->orderBy('create_time','DESC')
    	       ->skip(($pageNum - 1) * $pageSize)
    	       ->limit($pageSize)
    	       ->get();
    	$totalRecords = $this->count();
    	return array(
    		'data' => $result,
    	    'page_count' => ceil($totalRecords / $pageSize),
    	    'page_num' => $pageNum
    	);
    }
    
    public function alterItem($params)
    {
        if (!isset($params['item_id']) || empty($params['item_id'])){
            \Core::setError('参数错误');
        }
        $type = (int) $params['type'];
        $this->where(array(
            'item_id' => (int) $params['item_id']
        ));
        if ($type == 1){//删除
        	$this->update(array('is_delete' => 1));
        }else if ($type == 2){//推荐
        	$this->update(array('is_recommend' => 1));
        }elseif ($type == -2 ){ //取消推荐
            $this->update(array('is_recommend' => 0));
        }else{
            $status = (int)$params['status'];
            if ($status == 1 ){//下架
                $this->update(array('item_status' => 2));
            }else {//上架
                $this->update(array('item_status' => 1));
            }
        }
    	return array('item_id' => $params['item_id']);
    }
}



?>