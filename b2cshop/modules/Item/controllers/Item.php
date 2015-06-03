<?php
class ItemController extends Controllers_Abstract {
    
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

    public function listsAction()
    {
        $params = $this->getParams();
        $itemMapper = \Item\ItemModel::getInstance();
        $item = $itemMapper->cateList($params);
        $this->getView()->assign('items',$item);
    }
}
?>
