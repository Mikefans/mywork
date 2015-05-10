<?php

class IndexController extends Controllers_Abstract
{

    public function indexAction()
    {
        $this->disableView();
        $this->redirect('/home/index/index');
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

