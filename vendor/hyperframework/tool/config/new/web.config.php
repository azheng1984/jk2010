<?php
return array(
  'app/HomeScreen.php' => array(
    '<?php',
    'class HomeScreen {',
    '  public function render() {',
    "    echo 'Welcome!';",
    '  }',
    '}',
  ),
  'app/error/internal_server_error/InternalServerErrorScreen.php' => array(
    '<?php',
    'class InternalServerErrorScreen {',
    '  public function render() {',
    "    echo '500 Internal Server Error';",
    '  }',
    '}',
  ),
  'app/error/not_found/NotFoundScreen.php' => array(
    '<?php',
    'class NotFoundScreen {',
    '  public function render() {',
    "    echo '404 Not Found';",
    '  }',
    '}',
  ),
  'cache/' => 0777,
  'config/build.config.php' => array(
    '<?php',
    'return array(',
    "  'ClassLoader' => array('app', 'lib', HYPERFRAMEWORK_PATH.'web/lib'),",
    "  'Application' => array('Action', 'View' => 'Screen'),",
    ');',
  ),
  'config/error_handler.config.php' => array(
    '<?php',
    'return array(',
    "  '404 Not Found' => '/error/not_found',",
    "  '500 Internal Server Error' => '/error/internal_server_error',",
    ');',
  ),
  'lib/',
  'public/index.php' => array(
    '<?php',
    "define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);",
    "define('CACHE_PATH', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);",
    "define('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);",
    "define('HYPERFRAMEWORK_PATH', " . $GLOBALS['HYPERFRAMEWORK_PATH'] . ");",
    'require ' . $GLOBALS['CLASS_LOADER_PREFIX']
         . " . 'class_loader' . DIRECTORY_SEPARATOR .",
    "    'lib' . DIRECTORY_SEPARATOR . 'ClassLoader.php';",
    '$CLASS_LOADER = new Hyperframework\ClassLoader;',
    '$CLASS_LOADER->run();',
    '$APP = new Hyperframework\Web\Application;',
    '$EXCEPTION_HANDLER = new Hyperframework\Web\ExceptionHandler;',
    '$EXCEPTION_HANDLER->run();',
    '$APP->run();',
  ),
  'test/bootstrap.php' => array(
    '<?php',
    "define('TEST_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);",
    "define('ROOT_PATH', TEST_PATH.'fixture'.DIRECTORY_SEPARATOR);",
    "define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);",
    "define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);",
    "define('HYPERFRAMEWORK_PATH', ".$GLOBALS['HYPERFRAMEWORK_PATH'].');',
    'require '.$GLOBALS['CLASS_LOADER_PREFIX']
      .".'class_loader'.DIRECTORY_SEPARATOR",
    "  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';",
    '$CLASS_LOADER = new ClassLoader;',
    '$CLASS_LOADER->run();',
  ),
  'test/case/app/HomeScreenTest.php' => array(
    '<?php',
    'class HomeScreenTest extends PHPUnit_Framework_TestCase {',
    '  public function test() {',
    '  }',
    '}'
  ),
  'test/case/app/error/internal_server_error/InternalServerErrorScreenTest.php'
    => array(
      '<?php',
      'class InternalServerErrorScreen extends PHPUnit_Framework_TestCase {',
      '  public function test() {',
      '  }',
      '}'
    ),
  'test/case/app/error/not_found/NotFoundScreenTest.php' => array(
    '<?php',
    'class NotFoundScreenTest extends PHPUnit_Framework_TestCase {',
    '  public function test() {',
    '  }',
    '}'
  ),
  'test/fixture/cache/' => 0777,
  'test/fixture/config/build.config.php' => array(
    '<?php',
    "return array('ClassLoader' => array('lib'));",
   ),
  'test/fixture/lib/',
  'vendor/',
);
