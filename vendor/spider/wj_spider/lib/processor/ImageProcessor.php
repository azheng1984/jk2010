<?php
class ImageProcessor {
  public function execute($arguments) {
    $result = WebClient::get($arguments['domain'], '/'.$arguments['path']);
    if ($result['content'] === false) {
      return $result;
    }
    if (isset($GLOBALS['no_image_md5'])
      && isset($GLOBALS['no_image_md5'][md5($result['content'])])) {
      return;
    }
    $folder = IMAGE_PATH.$arguments['category_id'];
    if (!is_dir($folder)) {
      mkdir($folder, 0755, true);
    }
    file_put_contents(
      $folder.'/'.$arguments['id'].'.jpg', $result['content']
    );
  }
}