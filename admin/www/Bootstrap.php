<?php

class Bootstrap extends Yaf\Bootstrap_Abstract
{
    /**
     * 初始化插件
     * @param Yaf\Dispatcher $dispatcher            
     */
    public function _initPlugin(Yaf\Dispatcher $dispatcher)
    {
        if(!$dispatcher->getRequest()->isCli()){
            $authPlugin = new \AuthPlugin();
            $dispatcher->registerPlugin($authPlugin);
        }
    }

}
