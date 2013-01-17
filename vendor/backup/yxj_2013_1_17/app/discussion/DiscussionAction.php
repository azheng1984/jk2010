<?php
class DiscussionAction {
  public function GET() {}

  public function POST() {
    Db::insert(
      'topic',
      array(
        'book_id' => 1,
        'creation_time' => date('Y-m-d H:i:s'),
        'reply_time' => date('Y-m-d H:i:s'),
        'title' => $_POST['title'],
        'content' => $_POST['content']
      )
    );
  }
}