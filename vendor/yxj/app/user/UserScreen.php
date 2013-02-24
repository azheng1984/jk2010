<?php
class UserScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    
    echo '<h2>', $GLOBALS['USER']['name'], '</h2>';
    echo '<p><a href="flag">举报</a></p>';
    echo '<p>攻略分类</p>';
    $categoryList = Db::getAll('SELECT * FROM user_article_category WHERE user_id = ?', $_SESSION['user_id']);
    DbConnection::connect('youxuanji');
    foreach ($categoryList as $categoryMeta) {
      $category = Db::getRow('SELECT * FROM category WHERE id = ?', $categoryMeta['id']);
      echo '[<a href="category-', $category['id'], '/">', $category['name'], '</a>] x ', $categoryMeta['article_amount'];
    }
    DbConnection::close();
    echo '<hr />';
    echo '<p>参与 {2323} 篇攻略</p>';
    echo '<p>喜欢 {2323} 篇攻略</p>';
    echo '<p>关注 {2323} 篇攻略</p>';
//     echo '<p>喜欢</p>';
//     $articleList = Db::getAll('SELECT * FROM article_like WHERE user_id = ?', $_SESSION['user_id']);
//     foreach ($articleList as $article) {
//       $article = Db::getRow('SELECT * FROM article WHERE id = ?', $article['article_id']);
//       echo '<p>[#] <a href="/article-', $article['id'], '/">', $article['title'], '</a> - ', $article['abstract'], '</p> ';
//     }
    echo '<hr />';
    echo '<p>讨论</p>';
    echo '<p>主题：{2323}</p>';
    echo '<p>回复：{2323}</p>';
    echo '<p>关注：{2323}</p>';
    echo '<hr />';
    $articleList = Db::getAll('SELECT * FROM article WHERE user_id = ?', $_SESSION['user_id']);
    foreach ($articleList as $article) {
      echo '<p>[#] <a href="/article-', $article['id'], '/">', $article['title'], '</a> - ', $article['abstract'], '</p> ';
    }
  }
}