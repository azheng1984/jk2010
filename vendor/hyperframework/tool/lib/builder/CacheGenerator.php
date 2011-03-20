<?php
class CacheGenerator {
  public function generate($name, $cache) {
    file_put_contents(
      "cache/$name.cache.php",
      "<?php".PHP_EOL."return ".var_export($cache, true).';'
    );
  }
}