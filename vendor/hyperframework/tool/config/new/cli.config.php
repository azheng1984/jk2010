<?php
return array(
  'app/Command.php' => array(
    '<?php',
    'class Command {',
    '  public function execute() {',
    "    echo 'Welcome!'.PHP_EOL;",
    '  }',
    '}',
  ),
  'tmp/cache/' => 0777,
  'config/build.config.php' => array(
    '<?php',
    'return array(',
    "  'ClassLoader' => array('app', 'lib', HYPERFRAMEWORK_PATH.'cli/lib')",
    ');',
   ),
  'config/application.config.php' => array(
    '<?php',
    'return array(',
    "  'description' => 'Add your own description here',",
    "  'class' => 'Command'",
    ');',
  ),
  'lib/',
  'public/index.php' => array(
    0755,
    '#!/usr/bin/env php',
    '<?php',
    "define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);",
    "define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);",
    "define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);",
    "define('HYPERFRAMEWORK_PATH', ".$GLOBALS['HYPERFRAMEWORK_PATH'].');',
    'require '.$GLOBALS['CLASS_LOADER_PREFIX']
      .".'class_loader'.DIRECTORY_SEPARATOR",
    "  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';",
    '$CLASS_LOADER = new ClassLoader;',
    '$CLASS_LOADER->run();',
    '$EXCEPTION_HANDLER = new CommandExceptionHandler;',
    '$EXCEPTION_HANDLER->run();',
    '$APP = new CommandApplication;',
    '$APP->run();',
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
      .".'class_loader'.DIRECTORY_SEPARATOR",
    "  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';",
    '$CLASS_LOADER = new ClassLoader;',
    '$CLASS_LOADER->run();',
  ),
  'test/case/app/WelcomeCommandTest.php' => array(
    '<?php',
    'class CommandTest extends PHPUnit_Framework_TestCase {',
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
