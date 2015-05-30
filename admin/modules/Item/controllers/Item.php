<?php
class ItemController extends Controllers_Abstract {
    
    public function addAction() 
    {
        $params = $this->getParams();
        $cateModel = \Item\CategoryModel::getInstance();
        if (!isset($params['cid'])){
            $result = $cateModel->cateLists();
            $this->getView()->assign('cates',$result);
        }else {
            $cid = $params['cid'];
            $this->disableView();
        	$result = $cateModel->where(array(
        		'cate_father' => $cid
        	))
        	   ->get();
        	$this->response($result);
        }
    	
    	
    }
   
    public function addsAction()
    {
       $this->disableView();
       $params = $this->getParams();
       $model = \Item\ItemModel::getInstance();
       $result = $model->addItems($params);
       $this->response($result);
    }
   
    public function uploadAction() 
    {
        $this->disableView();
        $params = $this->getParams();
        $name = $params['name'];
        $uploadClass = new \Tools\UploadFile($name);
        $result = $uploadClass->getFileInfo();
        $this->response($result);
    }
}
?>

