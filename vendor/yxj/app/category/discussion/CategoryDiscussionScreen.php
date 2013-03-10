<?php
class CategoryDiscussionScreen extends Screen {
  private $isOther;
  private $list;

  public function __construct() {
    $id = $GLOBALS['PATH_SECTION_LIST'][1][1];
    if (strpos($id, '-other') !== false) {
      $id = substr($id, 0, strlen($id) - 6);
      $this->isOther = true;
    }
    $this->list = Db::getAll('SELECT ct.* FROM category_topic_category ctc LEFT JOIN category_topic ct ON ctc.category_topic_id = ct.id WHERE ctc.category_id = ?', $id);
  }

  protected function renderHtmlHeadContent() {
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="category_discussion" class="content">';
    echo '<ol>';
    foreach ($this->list as $item) {
      echo '<li>';
      echo '<div>用户 '.$item['user_id'],'</div>';
      echo '<div>'.$item['title'],'</div>';
      echo '<div>'.$item['content'],'</div>';
      echo '<div>回复 { ', $item['post_amount'], ' }</div>';
      echo '<div>赞 { ', $item['like_amount'], ' }</div>';
      echo '<div>关注 { ', $item['watch_amount'], ' }</div>';
      echo '<div>浏览 { ', $item['page_view'], ' }</div>';
      echo '<div>最后回复用户 { ', $item['last_post_user_id'], ' }</div>';
      echo '<div>最后回复时间 { ', $item['last_post_time'], ' }</div>';
      if ($this->isOther !== true) {
        echo '来自子分类:'.$item['category_id'];
      }
    }
    echo '</ol>';
    echo '</div>';
  }
}