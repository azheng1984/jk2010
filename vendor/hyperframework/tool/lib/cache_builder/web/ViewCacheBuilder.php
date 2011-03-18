<?php
class ViewCacheBuilder {
  public function build($dirPath, $entry, &$pathCache) {
    $suffix = substr($entry, -10);
    $entryCache = array();
    if ($suffix === 'Screen.php') {
      if (!isset($pathCache['View'])) {
        $pathCache['View'] = array();
      }
      $pathCache['View']['screen'] = preg_replace('/.php$/', '', $entry);
    }
    if (substr($entry, -9) === 'Image.php') {
      if (!isset($pathCache['View'])) {
        $pathCache['View'] = array();
      }
      $pathCache['View']['image'] = preg_replace('/.php$/', '', $entry);
    }
  }
}