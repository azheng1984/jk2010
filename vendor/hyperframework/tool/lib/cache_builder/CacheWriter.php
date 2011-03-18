<?php
class CacheWriter {
  public function write($name, $cache) {
    file_put_contents("cache/$name.cache.php", "<?php\nreturn ".var_export($cache, true).';');
  }
}