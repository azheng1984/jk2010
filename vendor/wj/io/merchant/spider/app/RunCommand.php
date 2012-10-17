<?php
class RunCommand {
  public function execute() {
    define('SPIDER_VERSION', 0);
    $processor = new JingdongCategoryListProcessor;
    $processor->execute();
    //foreach history not current version
  }
}