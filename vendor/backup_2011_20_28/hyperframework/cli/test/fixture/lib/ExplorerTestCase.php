<?php
abstract class ExplorerTestCase extends CliTestCase {
  protected function setUp() {
    ob_start();
  }

  protected function tearDown() {
    ob_end_clean();
    ExplorerContext::reset();
  }
}