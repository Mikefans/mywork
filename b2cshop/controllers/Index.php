<?php

class IndexController extends Controllers_Abstract
{

    public function indexAction()
    {
        $this->disableView();
        $this->redirect('/home/index/index');
    }
}
?>

