<?php
class ArticleAction {
  public function GET() {}

  public function POST() {
    Db::delete('article_draft', 'article_id = ?', $GLOBALS['ARTICLE_ID']);
    if ($_POST['submit'] === '发布') {
      Db::update('article', array('has_draft' => 0, 'abstract' => $_POST['abstract'], 'content' => $_POST['content']), 'id = ?', $GLOBALS['ARTICLE_ID']);
    } else {
      Db::insert('article_draft', array('abstract' => $_POST['abstract'], 'content' => $_POST['content'], 'article_id' => $GLOBALS['ARTICLE_ID'], 'modification_time' => date('Y-m-d H:i:s')));
      Db::update('article', array('has_draft' => 1), 'id = ?', $GLOBALS['ARTICLE_ID']);
    }
  }
}