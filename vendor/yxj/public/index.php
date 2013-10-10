<?php
//define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
//define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
//define('DATA_PATH', ROOT_PATH.'data'.DIRECTORY_SEPARATOR);
//define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);

function run() {
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    require ROOT_PATH . 'conifg/env.config.php';
    require Hyperframework\Config::get('Hyperframework\Path') . 'class_loader' .
        DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
    Hyperframework\ClassLoader::run();
    ExceptionHandler::run();
    $path = Router::execute();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
        $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
    }
    //TODO: 测试是否存在 session_id 的 cookie，如果存在，打开 session
    if ($path !== null) {
        $app = new Application;
        $app->run($path);
    }
}
run();
