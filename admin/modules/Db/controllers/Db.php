<?php
class DbController extends Controllers_Abstract {
    
   public function sqlAction() {
       $this->getView()->assign("content", "Hello World");
   }
   
   public function executeAction()
   {
       $this->disableView();
   	   $params = $this->getParams();
   	   $module = $params['module'];
   	   $table = $params['table'];
   	   $mm = "\\".$module."\\".$table."Model";
   	   $model = $mm::getInstance();
   	   $result = $model->select($params['select'])
   	                   ->where($params['where'])
   	                   ->limit(20)
   	                   ->get();
   	   echo "<pre>";
   	   var_dump($result);
   	   echo "</pre>";exit();

   }
}
?>

