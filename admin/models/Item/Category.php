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
    
    public function addCates($params)
    {
    	$cateFirst = $params['cate_first'];
    	$data = array(
    		'cate_name' => $cateFirst,
    	);
    	$cid = $this->insert($data);
    	$data = array();
    	$i = 0;
    	for ($i ;$i<10 ;$i++){
    		if (isset($params["cate_second{$i}"]) && !empty($params["cate_second{$i}"])){
    			$data[] = array(
    				'cate_name' => $params["cate_second{$i}"],
    				'cate_father' => $cid
    			);
    		}
    	}
    	if (!empty($data)){
    	    $this->insert($data);
    	}
    	return array('msg' => 'success');
    }
    
    public function delCates($params){
    	if (!isset($params['cate_id']) || empty($params['cate_id'])){
    		\Core::setError('参数错误');
    	}
        if (!isset($params['next_cate_id']) || empty($params['next_cate_id'])){
    		\Core::setError('参数错误');
    	}
    	if ($params['next_cate_id'] == $params['cate_id']){
    		$this->where(array(
    			'cate_father' => (int)$params['next_cate_id']
    		))
    		      ->whereOr(array(
    		      	'cate_id' => (int)$params['cate_id']
    		      ));
    	}else {
    	    $this->where(array(
    	        'cate_id' => (int)$params['next_cate_id']
    	    ))
    	    ->where(array(
    	    	'cate_father' => (int)$params['cate_id']
    	    ));
    	}
    	$this->update(array(
	       	'is_delete' => 1
	       ));
        return array('msg' => 'success');
    }
    
    public function alterCates($params){
        if (!isset($params['cate_name']) || empty($params['cate_name'])){
        	\Core::setError('类目名不能为空');
        }
        $cateName = $params['cate_name'];
        if (!isset($params['cate_id']) || empty($params['cate_id'])){
            \Core::setError('参数错误');
        }
        $cateId = $params['cate_id'];
        if (isset($params['flag'])){
        	$data = array(
        	    'cate_father' => $cateId,
        	    'cate_name' => $cateName
        	);
        	  $id = $this->insert($data);
        	  return array('cate_id' => $id);
        }
        if (isset($params['next_cate_id'])){
        	$nextCateId = (int)$params['next_cate_id'];
        	$this->where(array(
        		'cate_id' => $nextCateId
        	))
        	   ->where(array(
        	   	   'cate_father' => $cateId
        	   ));
        }else{
        	$this->where(array(
        		'cate_id' => $cateId
        	));
        }
        $this->update(array('cate_name' => $cateName));
        return array('msg' => 'success');
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