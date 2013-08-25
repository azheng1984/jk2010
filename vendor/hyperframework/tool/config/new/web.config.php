<?php
return array(
  'app/HomeScreen.php' => array(
    '<?php',
    'namespace Hft\Application;',
    '',
    'class HomeScreen {',
    '    public function render() {',
    "        echo 'Welcome!';",
    '    }',
    '}',
  ),
  'app/error/default/DefaultErrorScreen.php' => array(
    '<?php',
    'namespace Hft\Application;',
    '',
    'class DefaultErrorScreen {',
    '    public function render() {',
    "        echo Hyperframework\Web\ExceptionHandler" .
                 "::getException()->getCode();",
    '    }',
    '}',
  ),
  'app/error/internal_server_error/InternalServerErrorScreen.php' => array(
    '<?php',
    'namespace Hft\Application;',
    '',
    'class InternalServerErrorScreen {',
    '    public function render() {',
    "      echo '500 Internal Server Error';",
    '    }',
    '}',
  ),
  'app/error/not_found/NotFoundScreen.php' => array(
    '<?php',
    'namespace Hft\Application;',
    '',
    'class NotFoundScreen {',
    '    public function render() {',
    "      echo '404 Not Found';",
    '    }',
    '}',
  ),
  'cache/' => 0777,
  'config/build.config.php' => array(
    '<?php',
    "return array('ClassLoader', 'Application');",
  ),
  'config/application.config.php' => array(
    '<?php',
    "return array('Action', 'View' => 'Screen');"
  ),
  'config/class_loader.config.php' => array(
    '<?php',
    'return array(',
    "    'Hft' => array(",
    "        'Application' => array(",
    "            '@folder_mapping' => false,", 
    "            'app'", 
    "        ),",
    "        'lib'",
    "    ),",
    "    'Hyperframework\Web' => HYPERFRAMEWORK_PATH . 'web/lib'",
    ");"),
  'lib/',
  'public/index.php' => array(
    '<?php',
    "define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);",
    "define('CACHE_PATH', ROOT_PATH . ",
    "    'cache' . DIRECTORY_SEPARATOR);",
    "define('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);",
    "define('HYPERFRAMEWORK_PATH', " . $GLOBALS['HYPERFRAMEWORK_PATH'] . ");",
    'require ' . $GLOBALS['CLASS_LOADER_PREFIX']
         . " . 'class_loader' . DIRECTORY_SEPARATOR .",
    "    'lib' . DIRECTORY_SEPARATOR . 'ClassLoader.php';",
    'Hyperframework\ClassLoader::run();',
    'Hyperframework\Web\ExceptionHandler::run();',
    'Hyperframework\Web\Application::run();',
  ),
  'test/phpunit.xml' => array(
    '<phpunit bootstrap="./bootstrap.php" colors="true"></phpunit>'
  ),
  'test/bootstrap.php' => array(
    '<?php',
    "define('TEST_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);",
    "define('ROOT_PATH', TEST_PATH.'fixture'.DIRECTORY_SEPARATOR);",
    "define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);",
    "define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);",
    "define('HYPERFRAMEWORK_PATH', ".$GLOBALS['HYPERFRAMEWORK_PATH'].');',
    'require '.$GLOBALS['CLASS_LOADER_PREFIX']
      ." . 'class_loader'.DIRECTORY_SEPARATOR .",
    "    'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';",
    '$CLASS_LOADER = new Hyperframework\ClassLoader;',
    '$CLASS_LOADER->run();',
  ),
  'test/case/app/HomeScreenTest.php' => array(
    '<?php',
    'namespace Hft\Application;',
    '',
    'class HomeScreenTest extends \PHPUnit_Framework_TestCase {',
    '    public function test() {',
    '    }',
    '}'
  ),
  'test/case/app/error/internal_server_error/InternalServerErrorScreenTest.php'
    => array(
      '<?php',
      'namespace Hft\Application;',
      '',
      'class InternalServerErrorScreenTest extends \PHPUnit_Framework_TestCase {',
      '    public function test() {',
      '    }',
      '}'
    ),
  'test/case/app/error/not_found/NotFoundScreenTest.php' => array(
    '<?php',
     'namespace Hft\Application;',
    '',
    'class NotFoundScreenTest extends \PHPUnit_Framework_TestCase {',
    '    public function test() {',
    '    }',
    '}'
  ),
  'test/fixture/cache/' => 0777,
  'test/fixture/config/build.config.php' => array(
    '<?php',
    "return array('ClassLoader');",
  ),
  'test/fixture/config/class_loader.config.php' => array(
    '<?php',
    "return array('Hft\TestFixture' => 'lib');",
  ),
 'test/fixture/lib/',
  'vendor/',
);
