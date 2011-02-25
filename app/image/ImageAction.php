<?php
class ImageAction {
  public function GET() {
    $_ENV['media'] = 'image';
    if (!in_array($_GET['image_database_index'], $_ENV['image_database'])) {
      throw new NotFoundException;
    }
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
    $connection = new PDO('sqlite:'.DATA_PATH."image/{$_GET['image_database_index']}.sqlite");
    $statement = $connection->prepare("select $size from image where id=?");
    $statement->execute(array($_GET['id']));
    $cache = $statement->fetchColumn();
    if (!$cache) {
      throw new NotFoundException;
    }
    header('Content-type: image/jpeg');
    echo $cache;
  }
}