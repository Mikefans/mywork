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
   
    /**
     * 添加商品
     */
    public function addsAction()
    {
       $this->disableView();
       $params = $this->getParams();
       $model = \Item\ItemModel::getInstance();
       $result = $model->addItems($params);
       $this->response($result);
    }
   
    /**
     * 上传主图 缩略图
     */
    public function uploadAction() 
    {
        $this->disableView();
        $params = $this->getParams();
        $name = $params['name'];
        $uploadClass = new \Tools\UploadFile($name);
        $result = $uploadClass->getFileInfo();
        $this->response($result);
    }
    
    /**
     * 宝贝列表
     */
    public function listsAction()
    {
        $params = $this->getParams();
        $itemMapper = \Item\ItemModel::getInstance();
        $result = $itemMapper->itemList($params);
        $cateModel = \Item\CategoryModel::getInstance();
        $cateList = $cateModel->cateLists();
        $this->getView()->assign('lists',$result['data']);
        $this->getView()->assign('pageCount',$result['page_count']);
        $this->getView()->assign('status',$params['status']);
        $this->getView()->assign('cates',$cateList);
    }
    /**
     * 宝贝列表(异步)
     */
    public function listAction()
    {
        $this->disableView();
        $params = $this->getParams();
        $itemMapper = \Item\ItemModel::getInstance();
        $result = $itemMapper->itemList($params);
        $this->response($result);
    }
    
    
    /**
     * 宝贝操作
     */
    public function alterAction()
    {
        $this->disableView();
        $params = $this->getParams();
        $itemMapper = \Item\ItemModel::getInstance();
        $result = $itemMapper->alterItem($params);
        $this->response($result);
    }
}
?>

