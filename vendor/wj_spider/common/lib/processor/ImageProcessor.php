<?php
class ImageProcessor {
  public function execute($arguments) {
    $imageInfo = DbProduct::getImageInfo(
      $arguments['table_prefix'], $arguments['id']
    );
    $headers = array();
    if ($imageInfo['image_last_modified'] !== null) {
      $headers = array('If-Modified-Since: '.$imageInfo['image_last_modified']);
    }
    $result = WebClient::get(
      $arguments['domain'], '/'.$arguments['path'], $headers, null, true
    );
    if ($result['content'] === false) {
      return $result;
    }
    if ($result['http_code'] === 304) {
      return;
    }
    $lastModified = $this->getLastModified($result['header']);
    $md5 = md5($result['content']);
    if ($md5 === $imageInfo['image_md5']) {
      return;
    }
    if (isset($GLOBALS['no_image_md5'])
      && isset($GLOBALS['no_image_md5'][$md5])) {
      DbImage::deleteImage($arguments['table_prefix'], $imageInfo['id']);
    } else {
      DbImage::insertImage(
        $arguments['table_prefix'], $imageInfo['id'], $result['content']
      );
    }
    DbProduct::updateImageInfo(
      $arguments['table_prefix'], $imageInfo['id'], $md5, $lastModified
    );
    DbProductUpdate::insert(
      $arguments['table_prefix'], $imageInfo['id'], 'IMAGE'
    );
  }

  private function getLastModified($header) {
    preg_match('/Last-Modified: (.*?)\r\n/', $header, $matches);
    if (count($matches) === 2) {
      return $matches[1];
    }
  }
}