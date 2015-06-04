<?php
class RateController extends Controllers_Abstract {
    
    public function detailAction() 
    {
        $params = $this->getParams();
        $itemMapper = \Item\ItemModel::getInstance();
        $result = $itemMapper->getItemDetail($params);
        $rateMapper = \Item\RateModel::getInstance();
        $rate = $rateMapper->getItemRate($params);
        $this->getView()->assign('rate',$rate);
        $this->getView()->assign('item',$result);
    }

    public function addAction()
    {
        $this->disableView();
        $params = $this->getParams();
        $rateMapper = \Item\RateModel::getInstance();
        $result = $rateMapper->addRate($params);
        $this->response($result);
    }
    
    public function listAction()
    {
        $this->disableView();
        $params = $this->getParams();
        $rateMapper = \Item\RateModel::getInstance();
        $result = $rateMapper->addRate($params);
        $this->response($result);
    }
}
?>

