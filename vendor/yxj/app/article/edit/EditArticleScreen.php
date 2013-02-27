<?php
class EditArticleScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
    echo '<script src="/asset/js/markdown.js"></script>';
  }

  protected function renderHtmlBodyContent() {
    $article = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
    echo '<h1>', $article['title'], '</h1>';
    echo '<div class="todo">todo:编辑器整合 + 添加分段 + 图片上传</div>';
    NavigationScreen::render();
    echo '<form action="." method="POST">';
    echo '<textarea  id="abstract" name="abstract" style="width:500px;height:300px">',$article['abstract'],'</textarea>';
    echo '<textarea  id="content" name="content" style="width:500px;height:300px">';
    echo $article['content'],'</textarea>';
    echo '<input type="submit" value="发布" />';
    echo '<input type="submit" value="保存草稿" />';
    echo '</form>';
    ?>
    <?php
  }
}