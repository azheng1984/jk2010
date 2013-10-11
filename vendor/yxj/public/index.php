<?php
//define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
//define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
//define('DATA_PATH', ROOT_PATH.'data'.DIRECTORY_SEPARATOR);
//define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('Yxj\CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
define('Yxj\CACHE_PATH', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);

function initialize() {
    //require Config
    Config::set('Hyperframework\ConfigPath', CONFIG_PATH);
    Config::set('Hyperframework\CachePath', CACHE_PATH);
    require CONFIG_PATH . 'env.config.php';
    require HYPERFRAMEWORK_PATH . 'class_loader' .
        DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
    Hyperframework\ClassLoader::run();
    ExceptionHandler::run();
} initialize();

function run() {
    $path = Router::execute();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
        $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
    }
    //TODO: 测试是否存在 session_id 的 cookie，如果存在，打开 session
    if ($path !== null) {
        Application::run($path);
    }
} run();
