<?php
class ItemController extends Controllers_Abstract {
    
    public function addAction() 
    {
    	$cateModel = \Item\CategoryModel::getInstance();
    	$result = $cateModel->cateLists();
    	$this->getView()->assign('cate',$result);
    }
   
    public function addCateAction()
    {
       $this->disableView();
       $params = $this->getParams();
       $model = \Item\CategoryModel::getInstance();
       $result = $model->addCates($params);
       $this->response($result);
    }
   

}
?>

