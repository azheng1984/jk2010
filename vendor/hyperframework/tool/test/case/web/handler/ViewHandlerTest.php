<?php
class ViewHandlerTest extends PHPUnit_Framework_TestCase {
  public function testNotView() {
    $handler = new ViewHandler(array('Screen'));
  }

  public function testInvalidType() {
    
  }

  public function testNoRenderingMethod() {
    
  }

  public function testPrivateRenderingMethod() {
  }

  public function testReturnCache() {
  }
}