<?php
class NewCliCommand {
  public function execute() {
    
  }

  public function getOptionConfig() {
    return array (
      'preview' => array (
        'short' => 'p',
        'description' => '',
      ),
      'quite' => array (
        'short' => 'q',
        'description' => '',
      ),
    );
  }
}