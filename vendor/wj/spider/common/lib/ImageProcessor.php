<?php
class ImageProcessor {
  public function execute($arguments) {
    $productId = $arguments['id'];
    $meta = DbProduct::getImageMeta(
      $arguments['table_prefix'], $productId
    );
    $headers = array();
    if ($meta['last_modified'] !== null) {
      $headers = array('If-Modified-Since: '.$meta['image_last_modified']);
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
    if ($md5 === $meta['image_md5']) {
      return;
    }
    $tablePrefix = $arguments['table_prefix'];
    $this->save($tablePrefix, $productId, $result['content'], $md5);
    DbProduct::updateImageMeta($tablePrefix, $productId, $md5, $lastModified);
    DbProductLog::insert($tablePrefix, $productId, 'IMAGE');
  }

  private function getLastModified($header) {
    preg_match('/Last-Modified: (.*?)\r\n/', $header, $matches);
    if (count($matches) === 2) {
      return $matches[1];
    }
  }

  private function  save($tablePrefix, $productId, $content, $md5) {
      if (isset($GLOBALS['NO_IMAGE_MD5'])
        && isset($GLOBALS['NO_IMAGE_MD5'][$md5])) {
      DbImage::deleteImage($tablePrefix, $productId);
      return;
    }
    DbImage::insertImage(
      $tablePrefix, $productId, $content
    );
  }
}