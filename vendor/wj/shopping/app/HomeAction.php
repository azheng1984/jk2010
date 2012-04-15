<?php
class HomeAction {
  public function GET() {
    $GLOBALS['HOME_CONFIG'] = require CONFIG_PATH.'home.config.php';
    $this->parseMerchantType();
    $this->parsePage();
    $this->buildSlideshow();
  }

  private function parseMerchantType() {
    $path = $GLOBALS['PATH_SECTION_LIST'][1];
    if ($path === '') {
      $path = '/';
    }
    if (isset($GLOBALS['HOME_CONFIG']['merchant_type_list'][$path]) === false) {
      throw new NotFoundException;
    }
    $GLOBALS['MERCHANT_TYPE'] =
      $GLOBALS['HOME_CONFIG']['merchant_type_list'][$path];
    $GLOBALS['MERCHANT_TYPE']['path'] = $path;
  }

  private function parsePage() {
    if (isset($_GET['page']) === false || is_numeric($_GET['page']) === false
      || $_GET['page'] < 1) {
      $GLOBALS['PAGE'] = 1;
      return;
    }
    $GLOBALS['PAGE'] = intval($_GET['page']);
  }

  private function buildSlideshow() {
    if ($GLOBALS['PAGE'] === 1
      && $GLOBALS['MERCHANT_TYPE']['path'] === '/') {
      $GLOBALS['SLIDESHOW'] =
        $GLOBALS['HOME_CONFIG']['slideshow'];
      return;
    }
    $slideshow = DbMerchantSlide::getList(
      $GLOBALS['MERCHANT_TYPE'][0], $GLOBALS['PAGE']
    );
    $GLOBALS['SLIDESHOW'] = array();
    foreach ($slideshow as $item) {
      $GLOBALS['SLIDESHOW'][$item['id']] = $item;
      $GLOBALS['SLIDESHOW'][$item['id']]['slide_list'] =
        explode(' ', $item['list']);
    }
  }
}