<?php
class ClassRecognizerTest extends PHPUnit_Framework_TestCase {
  public function testStartsWithLowerCase() {
    $this->assertNull($this->getClass('test.php'));
  }

  public function testStartsWithUpperCase() {
    $this->assertSame('Test', $this->getClass('Test.php'));
  }

  public function testNotSourceCodeFile() {
    $this->assertNull($this->getClass('Test'));
  }

  private function getClass($fileName) {
    $recognizer = new ClassRecognizer;
    return $recognizer->getClass($fileName);
  }
}