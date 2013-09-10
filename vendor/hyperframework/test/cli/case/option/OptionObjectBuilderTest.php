<?php
class OptionObjectBuilderTest extends CliTestCase {
  public function testClassDoesNotExist() {
    $class = 'Unknown';
    $this->setExpectedCommandException("Class $class does not exist");
    $this->verifyBuild('Unknown');
  }

  public function testInfiniteOption() {
    $this->setInputArguments('first_option_argument', 'second_option_argument');
    $this->verifyBuild('TestOption', array('infinite'));
  }

  public function testNoConstructorInfiniteOption() {
    $this->setInputArguments('option_argument');
    $this->verifyBuild('NoConstructorOption', array('infinite'));
  }

  public function testNoConstructorOption() {
    $this->setInputArguments();
    $this->verifyBuild('NoConstructorOption');
  }

  public function testArgumentLengthError() {
    $this->setExpectedCommandException();
    $this->setInputArguments();
    $this->verifyBuild();
  }

  public function testBuildOptionWithArgument() {
    $this->setInputArguments('--option', 'option_argument');
    $this->verifyBuild();
  }

  private function verifyBuild($class = 'TestOption', $config = array()) {
    $config['class'] = $class;
    $builder = new OptionObjectBuilder($config, new CommandReader);
    $this->assertSame($class, get_class($builder->build()));
  }
}