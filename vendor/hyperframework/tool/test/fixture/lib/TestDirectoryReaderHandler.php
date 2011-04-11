<?php
class TestDirectoryReaderHandler {
  public function handle($fileName, $relativeFolder, $rootFolder) {
    $GLOBALS['TEST_CALLBACK_TRACE'][] = array(
      'name' => __CLASS__.'->'.__FUNCTION__,
      'argument' => array (
        'file_name' => $fileName,
        'relative_folder' => $relativeFolder,
        'root_folder' => $rootFolder
      ),
    );
  }
}