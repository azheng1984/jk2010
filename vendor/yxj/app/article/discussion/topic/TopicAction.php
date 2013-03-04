<?php
class TopicAction {
  public function GET() {}

  public function POST() {
    Db::insert(
      'post',
      array(
        'topic_id' => $GLOBALS['PATH_SECTION_LIST'][4],
        'user_id' => $_SESSION['user_id'],
        'creation_time' => date('Y-m-d H:i:s'),
        'content' => $_POST['content']
      )
    );
    Db::update(
      'topic',
      array('last_post_time' => date('Y-m-d H:i:s')),
      'id = ?',
      $GLOBALS['PATH_SECTION_LIST'][4]
    );
  }
}