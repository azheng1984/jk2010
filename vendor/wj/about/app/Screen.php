<?php
class Screen {
  public function render() {
    $template = substr($_SERVER['REQUEST_URI'], 1);
    if ($template === false) {
      $template = 'home';
    }
    if (file_exists(ROOT_PATH.'template/'.$template.'.tpl.php')) {
      echo '<!DOCTYPE html><html><head><meta charset="UTF-8"/>',
        '<title>关于货比万家</title></head><body>';
      require ROOT_PATH.'template/'.$template.'.tpl.php';//TODO security
      echo '</body></html>';
    } else {
      throw new NotFoundException;
    }
  }
}