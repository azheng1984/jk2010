<?php
class SignInAction {
  public function GET() {}

  public function POST() {
    DbConnection::connect('youxuanji');
    $user = Db::getRow(
      'SELECT id, password_sha1_digest, password_modification_time FROM user WHERE email = ?', $_POST['email']
    );
    var_dump($user);
    if ($user === false) {
      echo 'user not found';
      return;
    }
    if ($user['password_sha1_digest'] === sha1($_POST['password'].$user['password_modification_time'], true)) {
      session_regenerate_id(true);
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
      Db::update('user', array('sign_in_time' => date('Y-m-d H:i:s')), 'id = ?', $user['id']);
      $GLOBALS['APP']->redirect('http://dev.youxuanji.com/');
      return;
    }
    echo 'password error';
  }
}