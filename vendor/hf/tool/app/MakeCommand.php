<?php
class MakeCommand {
  private $config;

  public function execute() {
    $this->config = require 'config/make.config.php';
    if (isset($this->config['class_loader']) || in_array('class_loader', $this->config)) {
      $config = isset($this->config['class_loader']) ? $this->config['class_loader'] : null;
      $builder = new ClassLoaderCacheBuilder($config);
      $builder->build();
    }
    if (isset($this->config['application']) || in_array('application', $this->config)) {
      $config = isset($this->config['application']) ? $this->config['application'] : null;
      $builder = new ApplicationCacheBuilder($config);
      $builder->build();
    }
  }
}