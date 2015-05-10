<?php
phpinfo();die;
define("APP_PATH",  realpath(dirname(__FILE__))); //网站路径
define("BP", realpath(dirname(__FILE__) . '/../')); //基础路径
define("PAGE", realpath(dirname(__FILE__) . '/../views/page/')); //网页布局
define("JS", realpath(dirname(__FILE__) . '/../views/js/')); //js
define("CSS", realpath(dirname(__FILE__) . '/../views/css/')); //js
$app  = new Yaf\Application(APP_PATH . "/../conf/app.ini",'shop');
$app->bootstrap()->run();
?>