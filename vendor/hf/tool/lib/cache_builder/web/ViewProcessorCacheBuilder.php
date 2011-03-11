<?php
class ViewProcessorCacheBuilder {
  public function build($entry, &$pathCache) {
    $suffix = substr($entry, -10);
    $entryCache = array();
    if ($suffix === 'Screen.php') {
      if (!isset($pathCache['view'])) {
        $pathCache['view'] = array();
      }
      $pathCache['view']['screen'] = preg_replace('/.php$/', '', $entry);
    }
    if (substr($entry, -9) === 'Image.php') {
      if (!isset($pathCache['view'])) {
        $pathCache['view'] = array();
      }
      $pathCache['view']['image'] = preg_replace('/.php$/', '', $entry);
    }
  }
}