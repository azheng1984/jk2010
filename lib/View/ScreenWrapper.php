<?php
class ScreenWrapper {
  private $content;

  public function __construct($content) {
    $this->content = $content;
  }

  public function render() {
    echo '<html>';
    echo '</html>';
  }
}