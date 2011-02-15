<?php
class ImageAction {
  public function GET() {
    $connection = new PDO('sqlite:'.DATA_PATH.'image/1.sqlite');
    $size = 'normal';
    if (!is_numeric($_GET['id'])) {
      $length = strlen($_GET['id']);
      if ($length > 2 && substr($_GET['id'], -2) === '_s' && is_numeric(substr($_GET['id'], 0, $length-2))) {
        $_GET['id'] = substr($_GET['id'], 0, $length-2);
        $size = 'small';
      } else {
        throw new NotFoundException;
      }
    }
    $statement = $connection->prepare("select * from image where id=?");
    $statement->execute(array($_GET['id']));
    $cache = $statement->fetch(PDO::FETCH_ASSOC);
    echo $cache['small'];
  }
}