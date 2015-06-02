<?php 
namespace Item;

class CategoryModel extends \Mapper\Abstracts
{
    
    protected $_name = 'item_category';
    
    protected $_primaryKey ='cate_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function cateLists()
    {
    	$fCate = $this->where(array(
    		'is_delete' => 0,
    	    'cate_father' => 0
    	))
    	   ->get();
    	$sCate = $this->where(array(
    		'is_delete' => 0,
    	    'cate_father' => array('!=',0)
    	))
    	   ->get();
    	foreach ($fCate as $key=> $cate){
    		foreach ($sCate as $cates){
    			if ($cate['cate_id'] == $cates['cate_father']){
    				$fCate[$key]['n_cate'][] = $cates;
    			}
    		}
    	}
    	return $fCate;
    }
}


?>