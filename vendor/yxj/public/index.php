<?php
//define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
//define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
//define('DATA_PATH', ROOT_PATH.'data'.DIRECTORY_SEPARATOR);
//define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);

function run() {
    define('Hyperframeowrk\APP_NAMESPACE', 'YouXuanJi');
    define('YouXuanJi\ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    define('YouXuanJi\CONFIG_PATH', YouXuanJi\ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
    define('YouXuanJi\CACHE_PATH' YouXuanJi\ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);
    require YouXuanJi\CONFIG_PATH . 'env.config.php';
    require YouXuanJi\HYPERFRAMEWORK_PATH . 'class_loader' .
        DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
    Hyperframework\ClassLoader::run();
    ExceptionHandler::run();
    $path = Router::execute();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
        $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
    }
    //TODO: 测试是否存在 session_id 的 cookie，如果存在，打开 session
    if ($path !== null) {
        Application::run($path);
    }
}
run();
