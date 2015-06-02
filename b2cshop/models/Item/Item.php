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
}



?>