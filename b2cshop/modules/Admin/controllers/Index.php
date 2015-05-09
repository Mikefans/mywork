<?php
class IndexController extends Controllers_Abstract {
    
    
   public function indexAction() {
       
      // $this->disableView();
      // $this->initView();
       //$this->getView()->display(APP_PATH. "/views/test.phtml");
       $view = new Tools\View();
       $view->render(APP_PATH. "/views/test1.phtml");
       $this->getView()->assign("content", "Hello World");
   }
   
   
   public function getAction(){
   //	echo 'dwa';die;
   //$this->getView()->disableView();
   
   }
}
?>

