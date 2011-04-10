<?php
class ArgumentVerifierTest extends CliTestCase {
  public function testInfinite() {
    $this->verify(1, 'fixedArgument', true);
  }

  public function testInfiniteWithLessThanExpectation() {
    $this->setExpectedCommandException(
      'Argument length error(expected:1-... actual:0)'
    );
    $this->verify(0, 'fixedArgument', true);
  }

  public function testLessThanExpectation() {
    $this->setExpectedCommandException(
      'Argument length error(expected:1 actual:0)'
    );
    $this->verify(0, 'fixedArgument');
  }

  public function testMoreThanExpectation() {
    $this->setExpectedCommandException(
      'Argument length error(expected:1 actual:2)'
    );
    $this->verify(2, 'fixedArgument');
  }

  public function testOptionalArgument() {
    $this->verify(1);
    $this->verify(2);
  }

  public function testOptionalArgumentWithLessThanExpectation() {
    $this->setExpectedCommandException(
      'Argument length error(expected:1-2 actual:0)'
    );
    $this->verify();
  }

  public function testOptionalArgumentWithInfiniteAndLessThanExpectation() {
    $this->setExpectedCommandException(
      'Argument length error(expected:1-... actual:0)'
    );
    $this->verify(0, 'optionalArgument', true);
  }

  public function testOptionalArgumentWithMoreThanExpectation() {
    $this->setExpectedCommandException(
      'Argument length error(expected:1-2 actual:3)'
    );
    $this->verify(3);
  }

  private function setExpectedArgumentException($message) {
    
  }

  private function verify(
    $length = 0, $method = 'optionalArgument', $isInfinite = false
  ) {
    $verifier = new ArgumentVerifier;
    $verifier->verify(
      new ReflectionMethod('TestMethod', $method), $length, $isInfinite
    );
  }
}