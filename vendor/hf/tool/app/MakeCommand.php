<?php
class MakeCommand {
  private $config;

  public function execute() {
    $this->config = require 'config/make.config.php';
    if (isset($this->config['class_loader'])) {
      $builder = ClassLoaderCacheBuilder;
      $builder->execute();
    }
    if (isset($this->config['application'])) {
      $builder = ApplicationCacheBuilder;
      $builder->execute();
    }
  }
}