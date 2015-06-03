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
}


?>