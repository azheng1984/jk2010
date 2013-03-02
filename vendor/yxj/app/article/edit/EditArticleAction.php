<?php
class EditArticleAction {
  public function GET() {}

  public function POST() {
    $article = Db::getRow('SELECT content, is_json_content FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
    if ($article['is_json_content'] === '0') {
      $content = array(1 => array('', $article['content']), $_POST['index'] => array($_POST['title'], ''));
      Db::update('article', array('content' => json_encode($content), 'is_json_content' => 1), 'id = ?', $GLOBALS['ARTICLE_ID']);
    } else {
      $content = json_decode($article['content'], true);
      $content[] = array($_POST['title'], '');
      Db::update('article', array('content' => json_encode($content)), 'id = ?', $GLOBALS['ARTICLE_ID']);
    }
  }
}