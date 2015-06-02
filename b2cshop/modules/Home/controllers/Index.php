<?php
class IndexController extends Controllers_Abstract {
    
    
   public function indexAction() 
   {
       $itemMapper = \Item\ItemModel::getInstance();
       $result = $itemMapper->getHomelists();
//        var_dump($result);die;
       $this->getView()->assign("hot", $result['hot']);
       $this->getView()->assign("new", $result['new']);
       $this->getView()->assign("recommend", $result['recommend']);
   }
   
}
?>

