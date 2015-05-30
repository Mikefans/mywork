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
}


?>