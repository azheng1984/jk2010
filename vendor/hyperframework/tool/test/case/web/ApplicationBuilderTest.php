<?php
!defined('TOOL_PATH')?define('TOOL_PATH', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR) : null;
!defined('CLI_LIB')?define('CLI_LIB', dirname(TOOL_PATH).'/cli/lib/') : null;
!defined('LIB_PATH')?define('LIB_PATH', TOOL_PATH.'lib'.DIRECTORY_SEPARATOR) : null;
!defined('APP_PATH')?define('APP_PATH', TOOL_PATH.'app'.DIRECTORY_SEPARATOR) : null;
require_once LIB_PATH.'web/ApplicationBuilder.php';
require_once LIB_PATH.'web/ViewAnalyzer.php';
require_once LIB_PATH.'web/ApplicationConfiguration.php';
require_once LIB_PATH.'web/ApplicationCache.php';
require_once LIB_PATH.'DirectoryReader.php';
require_once LIB_PATH.'web/ApplicationAnalyzer.php';
require_once CLI_LIB.'CommandException.php';

class ApplicationBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuild() {
    $builder = new ApplicationBuilder;
    $result = $builder->build(array('View' => array('Screen')));
  }
}