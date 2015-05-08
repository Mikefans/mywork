<?php
class ErrorController extends Yaf_Controller_Abstract {
   public function errorAction() {
       $this->getView()->assign("content", "Hello World");
   }
}
?>

