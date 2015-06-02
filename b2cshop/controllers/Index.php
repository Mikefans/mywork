<?php

class IndexController extends Controllers_Abstract
{

    public function indexAction()
    {
        $this->disableView();
        $this->redirect('/home/index/index');
    }
    
    public function errorAction()
    {
        $this->disableView();
        $msg = $this->getParam('msg','');
        $this->errorResponse($msg);
    }
}
?>

