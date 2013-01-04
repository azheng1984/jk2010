<?php
class BookScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = Db::getRow('SELECT * FROM book WHERE id = 1');
    echo '<h1>', $book['name'], '</h1>';
    NavigationScreen::render();
    $page = Db::getRow('SELECT * FROM page WHERE id IN ('.$book['page_id_list'].')');
    echo '<a href="/book/1/page/', $page['id'], '">', $page['name'] , '</a>';
//     $lineList = Db::getAll('SELECT * FROM line WHERE id IN ('.$page['line_id_list'].')');
//     foreach ($lineList as $line) {
//       echo '<p>'.$line['content'].'</p>';
//     }
    echo '<p>广告</p>';
  }
}