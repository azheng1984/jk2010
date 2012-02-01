<?php
class SearchScreen extends Screen {
  public function __construct() {
    header('Cache-Control: private, max-age=0');
    $GLOBALS['URI']['RESULTS'] = ProductSearch::search();
  }

  protected function renderHtmlHeadContent() {
    //TODO: add category & property
    echo '<title>', $GLOBALS['URI']['QUERY'], '价格、折扣、新款和销量排行 - 货比万家</title>';
      /*'<meta name="description"',
      ' content="', $GLOBALS['URI']['QUERY'], '价格、折扣、新款和销量排行，', $GLOBALS['URI']['QUERY'], '图片 - 网上购物，货比万家。"/>';*/
    $this->addCssLink('search');
    $this->addJsLink('search');
  }

  protected function renderHtmlBodyContent() {
    echo '<div class="ad">';
    AdSenseScreen::render();
    echo '</div>';
    BreadcrumbScreen::render();
    echo '<div id="search">';
    ResultScreen::render();
    echo '</div>';
    echo '<div class="ad bottom">';
    AdSenseScreen::render();
    echo '</div>';
    RelatedQueryScreen::render();
  }
}