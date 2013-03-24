<?php
class MA extends Application {
  protected function processAction($path) {
    echo 'before';
    parent::processAction($path);
    echo 'after';
  }
}