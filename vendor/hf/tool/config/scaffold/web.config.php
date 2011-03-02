<?php
return array(
  'app/home/HomeScreen.php',
  'app/error/internal_server_error/InternalServerErrorScreen.php' => array(
    '<?php',
    'class InternalServerErrorScreen {',
    '  public function render() {',
    "    echo 'internal server error';",
    '  }',
    '}',
  ),
  'app/error/not_found/NotFoundScreen.php' => array(
    '<?php',
    'class NotFoundScreen {',
    '  public function render() {',
    "    echo 'page not found';",
    '  }',
    '}',
  ),
  'cache/',
  'config/error_handler.config.php' => array(
    '<?php',
    'return array(',
    "  '404 Not Found' => '/error/not_found',",
    "  '500 Internal Server Error' => '/error/internal_server_error',",
    ');',
  ),
  'config/make.config.php',
  'lib/',
  'public/index.php' => array(
    '<?php',
    "define('ROOT_PATH', dirname(dirname(__FILE__)).'/');",
    "define('CACHE_PATH', ROOT_PATH.'cache/');",
    "define('CONFIG_PATH', ROOT_PATH.'config/');",
    "define('DATA_PATH', ROOT_PATH.'data/');",
    "require 'ClassLoader.php';",
    '$classLoader = new ClassLoader;',
    '$classLoader->run();',
    '$app = new Application(',
    "  array('action' => new ActionProcessor, 'view' => new ViewProcessor)",
    ');',
    '$errorHandler = new ErrorHandler($app);',
    '$errorHandler->run();',
    '$app->run();',
  ),
  'test/',
  'vendor/',
);