<?php
class EditArticleScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
    echo '<script src="/asset/js/markdown.js"></script>';
  }

  protected function renderHtmlBodyContent() {
    $article = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
    echo '<h1>', $article['title'], '</h1>';
    echo '<div class="todo">todo:编辑器整合 + 添加分段 + 图片上传 + 草稿 + 预览</div>';
    NavigationScreen::render();
    echo '<form class="edit" action="." method="POST">';
    if ($article['has_draft'] === '0') {
      echo '<textarea  id="abstract" name="abstract" style="width:500px;height:300px">',$article['abstract'],'</textarea>';
      echo '<textarea  id="content" name="content" style="width:500px;height:300px">';
      echo $article['content'],'</textarea>';
    } else {
      $articleD = Db::getRow('SELECT abstract,content FROM article_draft WHERE article_id = ?', $article['id']);
      echo '<textarea  id="abstract" name="abstract" style="width:500px;height:300px">',$articleD['abstract'],'</textarea>';
      echo '<textarea  id="content" name="content" style="width:500px;height:300px">';
      echo $articleD['content'],'</textarea>';
    }
    echo '<input name="submit" type="submit" value="保存草稿" />';
    if ($article['is_published'] === '1') {
      echo '<input name="submit" type="submit" value="还原到发布版本" />';
    }
    echo '<input name="submit" type="submit" value="预览" />';
    echo '<input class="publish" name="submit" type="submit" value="发布" />';
    echo '</form>';
    ?>
    <script type="text/javascript" charset="utf-8" src="/asset/editor/editor_config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/asset/editor/editor_all.js"></script>
    <script type="text/javascript">
    var ue = UE.getEditor('content');
    </script>
    <?php
  }
}