<?php
class ImageProcessor {
  public function execute(
    $tablePrefix, $productId, $imageLastModified, $imageMd5, $domain, $path
  ) {
    $headers = array();
    if ($imageLastModified !== null) {
      $headers = array('If-Modified-Since: '.$imageLastModified);
    }
    $result = WebClient::get($domain, '/'.$path, $headers, null, true);
    if ($result['content'] === false) {
      return $result;
    }
    if ($result['http_code'] === 304) {
      return;
    }
    $lastModified = $this->getLastModified($result['header']);
    $md5 = md5($result['content']);
    if ($md5 === $imageMd5) {
      return;
    }
    $this->save(
      $tablePrefix, $productId, $result['content'], $md5, $imageMd5 !== null
    );
    Db::update(
      $tablePrefix.'-product',
      array('image_md5' => $md5, 'image_last_modified' => $lastModified),
      'id = ?',
      $productId
    );
    Db::insert(
      $tablePrefix.'-log',
      array('type' => 'IMAGE', 'product_id' => $productId)
    );
  }

  private function getLastModified($header) {
    preg_match('/Last-Modified: (.*?)\r\n/', $header, $matches);
    if (count($matches) === 2) {
      return $matches[1];
    }
  }

  private function save($tablePrefix, $productId, $content, $md5, $isNew) {
    $isEmpty = isset($GLOBALS['no_image_md5'][$md5]);
    if ($isEmpty && $isNew) {
      ImageDb::delete($tablePrefix, $productId);
    }
    if ($isEmpty) {
      return;
    }
    if ($isNew) {
      ImageDb::insert($tablePrefix, $productId, $content);
      return;
    }
    ImageDb::update($tablePrefix, $productId, $content);
  }
}