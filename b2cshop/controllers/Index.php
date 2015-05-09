<?php

class IndexController extends Controllers_Abstract
{

    public function indexAction()
    {
        $this->getView()->assign("content", "Hello World");
    }

    public function loginAction()
    {
        $params = $this->getParams();
        echo '11';
        die();
    }

    public function logoutAction()
    {
    	
    }
}
?>

