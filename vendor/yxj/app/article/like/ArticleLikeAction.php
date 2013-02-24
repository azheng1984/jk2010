<?php
class ArticleLikeAction {
  public function GET() {
    if (isset($_SESSION['user_id'])) {
      if (Db::getColumn('SELECT id FROM article_like WHERE user_id = ? AND article_id = ?', $_SESSION['user_id'], $GLOBALS['ARTICLE_ID']) === false) {
        Db::insert('article_like', array('user_id' => $_SESSION['user_id'], 'article_id' => $GLOBALS['ARTICLE_ID']));
        Db::execute('UPDATE article SET like_amount = like_amount + 1 WHERE id = ?', $GLOBALS['ARTICLE_ID']);
      }
    }
    header('HTTP/1.1 302 Found');
    header('Location: http://dev.youxuanji.com/article-'.$GLOBALS['ARTICLE_ID'].'/');
  }
}