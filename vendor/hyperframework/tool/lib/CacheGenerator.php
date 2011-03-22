<?php
class CacheGenerator {
  public static function generate($name, $cache) {
    file_put_contents(
      "cache".DIRECTORY_SEPARATOR."$name.cache.php",
      "<?php".PHP_EOL."return ".var_export($cache, true).';'
    );
  }
}