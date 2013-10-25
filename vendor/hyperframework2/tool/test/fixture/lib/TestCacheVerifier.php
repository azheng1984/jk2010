<?php
class TestCacheVerifier {
  public function verify($case, $path) {
    $case->assertSame(
      '0777', substr(sprintf('%o', fileperms(dirname($path))), -4)
    );
    $case->assertSame(
      '<?php'.PHP_EOL."return 'data';", file_get_contents($path)
    );
  }
}