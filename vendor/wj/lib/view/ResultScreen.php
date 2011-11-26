<?php
class ResultScreen {
  public static function render($results) {
    echo '<div id="result">';
    SortScreen::render($results['total_found']);
    self::renderList($results);
    PaginationScreen::render('?', $results['total_found']);
    echo '</div>';
  }

  private static function renderList($results) {
    echo '<ol>';
    $query = $GLOBALS['URI']['QUERY'];
    foreach ($results['matches'] as $id => $content) {
      $product = DbProduct::get($id);
      $name = $product['title'];
      $title = str_replace($query, '<em>'.$query.'</em>', htmlspecialchars(mb_substr(html_entity_decode($product['title'], ENT_QUOTES, 'utf-8'), 0, 40, 'utf-8'), ENT_QUOTES, 'utf-8'));
      $description = str_replace($query, '<em>'.$query.'</em>', htmlspecialchars(mb_substr(html_entity_decode($product['description'], ENT_QUOTES, 'utf-8'), 0, 64, 'utf-8'), ENT_QUOTES, 'utf-8'));
      echo '<li><div class="image"><a rel="nofollow" target="_blank" href="/r/',
        $product['id'].'"><img alt="'.$name.'" src="http://img.huobiwanjia.com/'.$product['id'].'.jpg" /></a></div><h3><a rel="nofollow" target="_blank" href="/r/'.$product['id'].'">',
        $title, '</a></h3><div class="price">&yen;<span>',
        $product['lowest_price_x_100']/100,
        '</span></div><p>',
        $description.'&hellip;</p> <div class="merchant">京东商城</div></li>';
    }
    echo '</ol>';
  }
}