<?php
define("APP_PATH",  realpath(dirname(__FILE__))); //网站路径
define("BP", realpath(dirname(__FILE__) . '/../')); //基础路径
define("DS","/");
$app  = new Yaf\Application(BP . '/conf/app.ini','shop');
$app->bootstrap()->run();
?>