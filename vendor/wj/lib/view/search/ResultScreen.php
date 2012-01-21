<?php
class ResultScreen {
  public static function render() {
    echo '<div id="result">';
    if ($GLOBALS['URI']['RESULTS']['total_found'] !== 0) {
      ResultHeaderScreen::render($GLOBALS['URI']['RESULTS']['total_found']);
      self::renderList();
      PaginationScreen::render(
        SearchUriArgument::getCurrent(), $GLOBALS['URI']['RESULTS']['total_found']
      );
    }
    echo '</div>';
  }

  private static function renderList() {
    $query = $GLOBALS['URI']['QUERY'];
    echo '<div id="product_list">';
    foreach ($GLOBALS['URI']['RESULTS']['matches'] as $id => $content) {
      $product = DbProduct::get($id);
      $name = $product['title'];
      $title = str_replace($query, '<em>'.$query.'</em>', htmlspecialchars(mb_substr(html_entity_decode($product['title'], ENT_QUOTES, 'utf-8'), 0, 40, 'utf-8'), ENT_QUOTES, 'utf-8'));
      $description = str_replace($query, '<em>'.$query.'</em>', htmlspecialchars(mb_substr(html_entity_decode($product['description'], ENT_QUOTES, 'utf-8'), 0, 64, 'utf-8'), ENT_QUOTES, 'utf-8'));
      echo '<div><p class="image"><a href="http://www.360buy.com/" target="_blank" rel="nofollow"><img alt="'.$name.'" src="http://img.dev.huobiwanjia.com/'.$product['id'].'.jpg" /></a></p><h3><a href="http://www.360buy.com/'.$product['id'].'?source=huobiwanjia.com" target="_blank" rel="nofollow">',
        $title, '</a></h3><p class="price">&yen;<span>',
        $product['lowest_price_x_100']/100,
        '</span></p><p class="description">',
        $description.'&hellip;</p><p class="merchant">京东商城</p></div>';
    }
    echo '</div>';
  }
}