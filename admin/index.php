<?php
define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); //应用配置路径
define("BP", realpath(dirname(__FILE__) . '/.../')); //文件基础路径
$app  = new Yaf_Application(BP . "/conf/app.ini");
$app->bootstrap()->run();
?>