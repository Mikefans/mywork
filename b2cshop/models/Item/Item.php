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
    
    public function getHomeLists()
    {
    	$newResult = $this->where(array(
    		'is_delete' => 0,
    	    'item_status' => 1
    	))
    	       ->orderBy('create_time','DESC')
    	       ->skip(0)
    	       ->limit(4)
    	       ->get();
    	$hotResult = $this->where(array(
    		'is_delete' => 0,
    	    'item_status' => 1
    	))
    	       ->orderBy('create_time','DESC')
    	       ->skip(0)
    	       ->limit(4)
    	       ->get();
    	$recomendResult = $this->where(array(
    	    'is_delete' => 0,
    	    'item_status' => 1,
    	    'is_recommend' => 1
    	))
        	->orderBy('create_time','DESC')
        	->skip(0)
        	->limit(4)
        	->get();
    	return array(
    		'recommend' => $recomendResult,
    	    'hot' => $hotResult,
    	    'new' => $newResult
    	);
    }
    
    public function getItemDetail($params)
    {
        $itemId = (int)$params['item_id'];
        $result = $this->where(array(
        	'item_id' => $itemId,
            'is_delete' => 0,
            'item_status' => 1
        ))
            ->first();
        return $result;
    }
    
    public function cateList($params)
    {
        $pageNum = isset($params['page_num']) ? (int)$params['page_num'] : 1;
        $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 10;
    	$nextCate = isset($params['cate_next']) ? $params['cate_next'] : '';
    	$cateId = isset($params['cate_id']) ? $params['cate_id'] : '';
    	$this->where(array(
    		'item_status' => 1,
    	    'is_delete' => 0
    	));
    	if ($nextCate){
    		$this->where(array(
    			'cate_id_before' => $nextCate
    		));
    	}else {
    		$this->where(array(
    			'cate_id' => $cateId,
    		))
    		  ->whereOr(array(
    		  	'cate_id_before' => $cateId
    		  ));
    	}
    	$result = $this->orderBy('create_time','desc')
    	               ->skip(($pageNum -1 ) * $pageSize)
    	               ->limit($pageSize)
    	               ->get();
    	return $result;
    }
}



?>