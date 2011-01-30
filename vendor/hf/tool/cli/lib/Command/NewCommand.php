<?php
class NewCommand {
  public function execute($context) {
    $color = $context->getOption('color'); //if null throw exception
  }
}