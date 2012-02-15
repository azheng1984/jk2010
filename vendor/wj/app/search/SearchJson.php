<?php
class SearchJson extends Json {
  public function __construct() {
    if ($GLOBALS['PAGE'] > 5) {
      throw new NotFoundException;
    }
  }

  protected function renderJson() {
    
  }
}