<?php
class CacheWriter {
  public function write($name, $cache) {
    file_put_contents(
      "cache/$name.cache.php",
      "<?php".PHP_EOL."return ".var_export($cache, true).';'
    );
  }
}