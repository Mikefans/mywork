<?php
error_reporting(E_ALL | E_STRICT);

if (! isset($argv[1])) {
    echo "请选择需要生成的模块配置";
    die();
}
$config = array(
    "module" => $argv[1]
);
if (isset($argv[2])) {
    $config['table'] = $argv[2];
}

if (isset($argv[3])){
    $config['namespace_prefix'] = $argv[3];
} 

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', realpath(dirname(dirname(__DIR__)) . DS));
define("APP_PATH", BP . DS . "app");
define("APP_CONF_PATH", APP_PATH . DS . "conf");
$app = new Yaf\Application(APP_CONF_PATH . "/app.ini");
$app->bootstrap()->execute("main");

function main()
{
    global $config;
    $builder = new Hlg\Model\Builder();
    $builder->run($config);
}

