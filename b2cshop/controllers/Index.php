<?php
class IndexController extends Controllers_Abstract {
   
   public function indexAction()
   {
       $this->getView()->assign("content", "Hello World");
   }

}
?>

