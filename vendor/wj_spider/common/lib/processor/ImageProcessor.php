<?php
class ImageProcessor {
  public function execute($arguments) {
    $result = WebClient::get($arguments['domain'], '/'.$arguments['path']);
    if ($result['content'] === false) {
      return $result;
    }
    //todo:check last modified
    $imageInfo = DbProduct::getImageInfo(
      $arguments['table_prefix'], $arguments['id']
    );
    $md5 = md5($result['content']);
    if ($md5 === $imageInfo['image_md5']) {
      return;
    }
    if (isset($GLOBALS['no_image_md5'])
      && isset($GLOBALS['no_image_md5'][$md5])) {
      return;
    }
    DbImage::insertImage(
      $arguments['table_prefix'], $imageInfo['id'], $result['content']
    );
    DbProduct::updateImageInfo(
      $arguments['table_prefix'], $imageInfo['id'], $md5, null
    );
  }
}