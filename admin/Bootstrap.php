<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{
    
    /**
     * 把配置存到注册表
     */
    public function _initConfig()
    {
        //echo 'dwa';die; 
//         $dbConfig = new Yaf_Config_Ini(APP_CONF_PATH . "/db.ini", Hlg::environ());
//         Yaf\Registry::set("appConfig", Yaf_Application::app()->getConfig());
//         Yaf\Registry::set("dbConfig", $dbConfig);
    }

    /**
     * 初始化插件
     * 
     * @param Yaf\Dispatcher $dispatcher            
     */
    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        if(!$dispatcher->getRequest()->isCli()){
//             $restPlugin = new RestPlugin();
//             $dispatcher->registerPlugin($restPlugin);
            $authPlugin = new AuthPlugin();
            $dispatcher->registerPlugin($authPlugin);
        }
    }

//     /**
//      * 初始化视图
//      */
//     public function _initView(\Yaf\Dispatcher $dispatcher)
//     {
//         $viewPath = \Yaf\Registry::get("appConfig")->application->view->path;
//         $hlgView = new Hlg\View($viewPath);
//         $hlgView->setLayout("page");
//         Yaf\Dispatcher::getInstance()->setView($hlgView);
//     }

//     /**
//      * 初始化whoops debug
//      */
//     public function _initDebug()
//     {
//         if (! Yaf\Registry::get("appConfig")->application->dispatcher->catchException) {
//             $whoops = new \Whoops\Run();
//             $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
//             $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
//             $jsonHandler = new \Whoops\Handler\JsonResponseHandler();
//             $jsonHandler->addTraceToOutput(true);
//             $jsonHandler->onlyForAjaxRequests(true);
//             $whoops->pushHandler($jsonHandler);
//             $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
//             $whoops->sendHttpCode(500);
//             $whoops->register();
//         }
//     }

//     /**
//      * 初始化自动加载路径
//      */
//     public function _initAutoload()
//     {
//         Hlg::initAutoload();
//     }

//     /**
//      * 初始化错误捕捉
//      * 
//      * @param \Yaf\Dispatcher $dispatcher            
//      */
//     public function _initErrorHandler(\Yaf\Dispatcher $dispatcher)
//     {
//         $dispatcher->setErrorHandler(array(
//             "Exception\Parser",
//             "errorHandler"
//         ));
//         register_shutdown_function(array(
//             "Exception\Parser",
//             "shutDownHandler"
//         ));
//     }
}
