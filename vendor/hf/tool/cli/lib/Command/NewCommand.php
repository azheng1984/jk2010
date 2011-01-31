<?php
class NewCommand {
  public function execute($context, $arg1, $arg2 = null) {
    $color = $context->getOption('color'); //if null throw exception
  }
}