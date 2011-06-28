<?php
class ImageProcessor {
  public function execute($arguments) {
    $result = WebClient::get($arguments['domain'], $arguments['path']);
    $path = $arguments['path'];
    $folder = ROOT_PATH.'image/'.$arguments['category_id'];
    if (!is_dir($path)) {
      mkdir($folder, 0777, true);
    }
    file_put_contents(
      $folder.'/'.$arguments['id'].'jpg', $result['content']
    );
  }
}