<?php
class BookScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = Db::getRow('SELECT * FROM book WHERE id = 1');
    echo '<h1>', $book['name'], '</h1>';
    NavigationScreen::render();
//     $pageIdList = explode("\n", $book['page_id_list']);
//     $pageList = Db::getAll('SELECT * FROM page WHERE id IN ('.implode(',', $pageIdList).')');
//     echo '<ol>';
//     $index = 0;
//     foreach ($pageList as $page) {
//       if ($pageIdList[$index][0] === ' ') {
//         echo '<ul>';
//       }
//       echo '<li><a href="/book/1/page/', $page['id'], '">',Db::getColumn('SELECT content FROM line WHERE id = '.$page['name_line_id']).'</a></li>';
//       if ($pageIdList[$index][0] === ' ') {
//         echo '</ul>';
//       }
//       $index++;
//     }
    echo '</ol>';
    echo '<p>广告</p>';
  }
}