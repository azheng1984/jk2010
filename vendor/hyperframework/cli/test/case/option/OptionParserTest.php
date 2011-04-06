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
    $item = '-ab';
    $config = array();
    $_SERVER['argv'] = array('index.php', $item);
    $_SERVER['argc'] = 2;
    $reader = new CommandReader;
    $parser = new OptionParser($reader, $config);
    $this->assertNull($parser->parse());
    foreach (array('a', 'b') as $short) {
      $reader->moveToNext();
      $this->assertEquals('-'.$short, $reader->get());
    }
  }

  public function testExpansion() {
    $config = array('alias' => array('expansion' => 'target'));
    $_SERVER['argv'] = array('index.php', '--alias');
    $_SERVER['argc'] = 2;
    $reader = new CommandReader;
    $parser = new OptionParser($reader, $config);
    $this->assertNull($parser->parse());
    $reader->moveToNext();
    $this->assertEquals('target', $reader->get());
  }

  public function testCreateFlagOption() {
    $config = array('test');
    $_SERVER['argv'] = array('index.php', '--test');
    $_SERVER['argc'] = 2;
    $reader = new CommandReader;
    $parser = new OptionParser($reader, $config);
    $this->assertEquals(array('test', true), $parser->parse());
  }

  public function testCreateObjectOption() {
    $config = array('test' => 'TestOption');
    $_SERVER['argv'] = array('index.php', '--test', 'argument');
    $_SERVER['argc'] = 3;
    $reader = new CommandReader;
    $parser = new OptionParser($reader, $config);
    list($name, $value) = $parser->parse();
    $this->assertEquals('TestOption', get_class($value));
  }
}