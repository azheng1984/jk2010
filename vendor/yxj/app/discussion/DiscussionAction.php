<?php
class DiscussionAction {
  public function GET() {}

  public function POST() {
    Db::insert(
      'topic',
      array(
        'article_id' => $GLOBALS['ARTICLE_ID'],
        'creation_time' => date('Y-m-d H:i:s'),
        'last_post_time' => date('Y-m-d H:i:s'),
        'title' => $_POST['title'],
        'content' => $_POST['content']
      )
    );
  }
}