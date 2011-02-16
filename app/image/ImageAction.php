<?php
class ImageAction {
  public function GET() {
    $connection = new PDO('sqlite:'.DATA_PATH.'image/1.sqlite');
    $size = 'normal';
    if (!is_numeric($_GET['id'])) {
      $length = strlen($_GET['id']);
      if ($length > 2 && substr($_GET['id'], -2) === '_s' && is_numeric(substr($_GET['id'], 0, $length-2))) {
        $_GET['id'] = substr($_GET['id'], 0, $length - 2);
        $size = 'small';
      } else {
        throw new NotFoundException;
      }
    }
    $statement = $connection->prepare("select $size from image where id=?");
    $statement->execute(array($_GET['id']));
    $cache = $statement->fetchColumn();
    if (!$cache) {
      header('HTTP/1.1 404 Not Found');
      return;
    }
    header('Content-type: image/jpeg');
    echo $cache;
  }
}