<?php
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('Yxj\CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
define('Yxj\CACHE_PATH', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);
define('Yxj\DATA_PATH', ROOT_PATH . 'data' . DIRECTORY_SEPARATOR);
define(
    'Yxj\HYPERFRAMEWORK_PATH', '/srv/lib/hyperframework/lib/Hyperframework/'
);

function initialize() {
    require HYPERFRAMEWORK_PATH . 'Config.php';
    //Hyperframework\Config::setRootPath(ROOT_PATH);
    require CONFIG_PATH . 'env.config.php';
    require HYPERFRAMEWORK_PATH . 'ClassLoader.php';
    Hyperframework\ClassLoader::run();
    Hyperframework\ExceptionHandler::run();
} initialize();

function run() {
    $path = Router::execute();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
        $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
    }
    //TODO: 测试是否存在 session_id 的 cookie，如果存在，打开 session
    if ($path !== null) {
        Hyperframework\Application::run($path);
    }
} run();

//function finalize() {
//} finalize();
