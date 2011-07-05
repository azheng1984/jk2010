<?php
class ImageProcessor {
  const NO_IMAGE_MD5 = '074f08d6ad97e753c7d99553c7fa530a';

  public function execute($arguments) {
    $result = WebClient::get($arguments['domain'], '/'.$arguments['path']);
    if ($result['content'] === false) {
      return $result;
    }
    if (md5($result['content']) === self::NO_IMAGE_MD5) {
      return;
    }
    $folder = ROOT_PATH.'image/'.$arguments['category_id'];
    if (!is_dir($folder)) {
      mkdir($folder, 0777, true);
    }
    file_put_contents(
      $folder.'/'.$arguments['id'].'.jpg', $result['content']
    );
  }
}