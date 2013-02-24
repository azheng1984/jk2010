<?php
class ArticleAction {
  public function GET() {}

  public function POST() {
    Db::update('article', array('abstract' => $_POST['abstract'], 'content' => $_POST['content']), 'id = ?', $GLOBALS['ARTICLE_ID']);
  }
}