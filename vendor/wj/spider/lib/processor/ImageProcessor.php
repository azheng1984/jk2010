<?php
class ImageProcessor {
  public function execute($arguments) {
    $productId = $arguments['id'];
    $meta = Db::getRow(
      'SELECT image_last_modified, image_md5 FROM '
        .$arguments['table_prefix'].'_product WHERE id = ?',
      $productId
    );
    $headers = array();
    if ($meta[] !== null) {
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
    Db::update(
      $tablePrefix.'_product',
      array('image_md5' => $md5, 'image_last_modified' => $lastModified),
      'id = ?',
      $productId
    );
    Db::insert(
      $tablePrefix.'_log',
      array('type' => 'IMAGE', 'product_id' => $productId)
    );
  }

  private function getLastModified($header) {
    preg_match('/Last-Modified: (.*?)\r\n/', $header, $matches);
    if (count($matches) === 2) {
      return $matches[1];
    }
  }

  private function save($tablePrefix, $productId, $content, $md5) {
      if (isset($GLOBALS['no_image_md5'])
      && isset($GLOBALS['no_image_md5'][$md5])) {
      ImageDb::delete($tablePrefix, $productId);
      return;
    }
    ImageDb::insert($tablePrefix, $productId, $content);
  }
}