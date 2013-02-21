<?php
class NewArticleAction {
  public function GET() {
  }

  public function POST() {
    $time = date('Y-m-d H:i:s');
    $id = $this->getArticleId();
    Db::insert('article', array(
      'id' => $id,
      'title' => $_POST['title'],
      'abstract' => $_POST['abstract'],
      'content' => $_POST['content'],
      'creation_time' => $time,
      'modification_time' => $time,
      'category_id' => '1',
      'user_id' => $_SESSION['user_id']
    ));
    header('HTTP/1.1 302 Found');
    header('Location: http://dev.youxuanji.com/article-'.$id.'/');
  }

  private function getArticleId() {
    $path = ROOT_PATH.'data/last_article_id.php';
    $id = require $path;
    ++$id;
    if (strpos($id, '4') !== false) {
      $id = str_replace('4', '5', $id);
    }
    file_put_contents($path, '<?php return '.$id.';');
    return $id;
  }
}