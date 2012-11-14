<?php
class SphinxIndex {
  public static function update() {
    system('indexer delta --config sphinx.conf');
  }
}