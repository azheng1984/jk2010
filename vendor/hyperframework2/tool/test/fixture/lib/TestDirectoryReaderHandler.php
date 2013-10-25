<?php
class TestDirectoryReaderHandler {
  public function handle($fullPath, $relativeFolder, $rootFolder) {
    $GLOBALS['TEST_CALLBACK_TRACE'][] = array(
      'name' => __CLASS__.'->'.__FUNCTION__,
      'argument' => array(
        'full_path' => $fullPath,
        'relative_folder' => $relativeFolder,
        'root_folder' => $rootFolder
      ),
    );
  }
}