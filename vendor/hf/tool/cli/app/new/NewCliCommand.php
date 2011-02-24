<?php
class NewCliCommand {
  public function execute() {
    $config = require HF_PATH.'tool/core/config/scaffold/cli_scaffold.config.php';
    echo var_export($config);
  }
}