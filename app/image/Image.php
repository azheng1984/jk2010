<?php
class Image {
  public function render() {
    if (!in_array($_GET['image_database_index'], $_ENV['image_database'])) {
      throw new NotFoundException;
    }
    $size = 'normal';
    $id = $_GET['id'];
    if (!is_numeric($id)) {
      $length = strlen($id);
      if ($length > 2
        && substr($id, -2) === '_s'
        && is_numeric($prefix = substr($id, 0, $length - 2))) {
        $id = $prefix;
        $size = 'small';
      } else {
        throw new NotFoundException;
      }
    }
    $connection = new PDO(
      'sqlite:'.DATA_PATH."image/{$_GET['image_database_index']}.sqlite"
    );
    $statement = $connection->prepare("select $size from image where id=?");
    $statement->execute(array($id));
    $image = $statement->fetchColumn();
    if ($image === false) {
      throw new NotFoundException;
    }
    header('Content-type: image/jpeg');
    echo $image;
  }
}