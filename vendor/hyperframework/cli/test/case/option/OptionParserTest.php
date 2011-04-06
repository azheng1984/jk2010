<?php
class OptionParserTest extends CliTestCase {
  public function testNotAllowedOption() {
    $item = '--test';
    $this->setExpectedCommandException("Option '$item' not allowed");
    $config = array();
    $_SERVER['argv'] = array('index.php', $item);
    $_SERVER['argc'] = 2;
    $parser = new OptionParser(new CommandReader, $config);
    $parser->parse();
  }

  public function testCombinedOption() {
    
  }

  public function testExpansion() {
    
  }

  public function testCreateFlagOption() {
    
  }

  public function testCreateObjectOption() {
    
  }
}