<?php
class EditArticleScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
    echo '<script src="/asset/js/markdown.js"></script>';
  }

  protected function renderHtmlBodyContent() {
    $article = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
    echo '<p>标题: ', $article['title'], '</h1></p>';
    echo '<p>分类: ', $article['category_id'], '</p>';
//    echo '<div class="">目录</div> 添加章节 <form action="edit" method="POST">';
//      echo '<p>序号: <input name="index" /> 标题: <input name="title" /> <button type="submit">添加</button></p></form>';
    NavigationScreen::render();
    echo '<form class="edit" action="." method="POST">';
    if ($article['has_draft'] === '0') {
      echo '<textarea  id="abstract" name="abstract" style="width:500px;height:300px">',$article['abstract'],'</textarea>';
      if ($article['is_json_content'] === '0') {
        echo '<textarea  id="content" name="content" style="width:500px;height:300px">';
        echo $article['content'],'</textarea>';
      } else {
        $this->printJsonContent($article['content']);
      }
    } else {
      $articleD = Db::getRow('SELECT abstract,content FROM draft WHERE article_id = ?', $article['id']);
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
    //var ue = UE.getEditor('content');
    </script>
    <?php
  }
  private function printJsonContent($content) {
    $content = json_decode($content, true);
    //echo '<div id="content">'.$this->book['content'].'</div>';
    $index = 1;
    foreach ($content as $section) {
      echo '<div class="section"><h2><span class="index">', $index, '</span> ', $section[0],'</h2> <p><a href="">编辑</a></p><p>', $section[1], ' <a href="">编辑</a></p>', '</div>';
      ++$index;
    }
    echo '</div>';
  }
}