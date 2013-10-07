<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('DATA_PATH', ROOT_PATH.'data'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
require CONFIG_PATH . 'env-global.config.php';
require CONFIG_PATH . 'env-local.config.php';
require HYPERFRAMEWORK_PATH . 'class_loader' . DIRECTORY_SEPARATOR .
    'lib' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
//$CLASS_LOADER = new ClassLoader;
Hyperframework\ClassLoader::run();
$EXCEPTION_HANDLER = new ExceptionHandler;
$EXCEPTION_HANDLER->run();
$path = Router::execute();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
    $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
}
//TODO: 测试是否存在 session_id 的 cookie，如果存在，打开 session
if ($path !== null) {
    $APP = new Application;
    $APP->run($path);
}
