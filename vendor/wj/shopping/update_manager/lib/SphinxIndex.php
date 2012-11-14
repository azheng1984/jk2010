<?php
class SphinxIndex {
  public static function indexDelta() {
    system('indexer delta --config sphinx.conf');
  }

  public static function indexMain() {
    system('indexer main --config sphinx.conf');
  }
}