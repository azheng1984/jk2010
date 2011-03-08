<?php
class MakeCommand {
  private $config;

  public function execute() {
    $this->config = require 'config/make.config.php';
    if (isset($this->config['class_loader'])) {
      $builder = new ClassLoaderCacheBuilder($this->config['class_loader']);
      $builder->build();
    }
    if (isset($this->config['application'])) {
      $builder = new ApplicationCacheBuilder($this->config['application']);
      $builder->build();
    }
  }
}