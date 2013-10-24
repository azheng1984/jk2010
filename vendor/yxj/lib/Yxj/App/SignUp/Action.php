<?php
class SignUpAction {
  public function GET() {
  }

  public function POST() {
    $time = date('Y-m-d H:i:s');
    DbConnection::connect('youxuanji');
    Db::insert('user', array(
      'id' => $this->getNewUserId(),
      'email' => $_POST['email'],
      'password_sha1_digest' => sha1($_POST['password'].$time, true),
      'password_modification_time' => $time,
      'name' => $_POST['name'],
      'sign_up_time' => $time,
      'sign_in_time' => $time,
    ));
  }

  private function getNewUserId() {
    $path = ROOT_PATH.'data/last_user_id.php';
    $id = require $path;
    ++$id;
    if (strpos($id, '4') !== false) {
      $id = str_replace('4', '5', $id);
    }
    file_put_contents($path, '<?php return '.$id.';');
    return $id;
  }
}
