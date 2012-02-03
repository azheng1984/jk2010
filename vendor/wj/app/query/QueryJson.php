<?php
class QueryJson extends EtagView {
  public function __construct() {
    header('Cache-Control: public, max-age=3600');
  }

  public function renderBody() {
    QuerySearch::search();
  }
}