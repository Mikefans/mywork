<?php 
namespace Shop\Mapper\Left;

class CatalogueModel extends \Mapper\Abstracts
{
    
    protected $_name = 'shop_left_catalogue';
    
    protected $_primaryKey ='cid';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function getLists()
    {
    	$result = $this->where(array(
    		'is_delete' => 0
    	))
    	   ->get();
    	return $result;
    }
}


?>