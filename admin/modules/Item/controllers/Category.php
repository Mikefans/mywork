<?php
class CategoryController extends Controllers_Abstract {
    
    public function addAction() 
    {}
   
    public function addCateAction()
    {
       $this->disableView();
       $params = $this->getParams();
       $model = \Item\CategoryModel::getInstance();
       $result = $model->addCates($params);
       $this->response($result);
    }
   
   public function delCateAction()
   {
       $this->disableView();
       $params = $this->getParams();
       $model = \Item\CategoryModel::getInstance();
       $result = $model->delCates($params);
       $this->response($result);
   }
   
   public function listAction()
   {
       $model = \Item\CategoryModel::getInstance();
       $result = $model->cateLists();
       $this->getView()->assign('result',$result);
   }
   
    public function alterCateAction()
    {
       $this->disableView();
       $params = $this->getParams();
       $model = \Item\CategoryModel::getInstance();
       $result = $model->alterCates($params);
       $this->response($result);
    }
}
?>

