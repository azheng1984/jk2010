<?php
class CacheExplorerTest {
  public function test () {
    $path = ROOT_PATH.'cache';
    if (is_dir($path)) {
      rmdir($path);
    }
     $this->assertSame(
      '0777', substr(sprintf('%o', fileperms(ROOT_PATH.'cache')), -4)
    );
  }
}