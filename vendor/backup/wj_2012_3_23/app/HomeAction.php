<?php
class HomeAction {
  public function GET() {
    $GLOBALS['HOME_CONFIG'] = require CONFIG_PATH.'home.config.php';
    $this->parseMerchantType();
    $this->parsePage();
    $this->buildMerchantSlideList();
  }

  private function parseMerchantType() {
    $path = $GLOBALS['PATH_SECTION_LIST'][1];
    if ($path === '') {
      $path = '/';
    }
    if (isset($GLOBALS['HOME_CONFIG']['merchant_type_list'][$path]) === false) {
      throw new NotFoundException;
    }
    $GLOBALS['MERCHANT_TYPE_CONFIG'] =
      $GLOBALS['HOME_CONFIG']['merchant_type_list'][$path];
    $GLOBALS['MERCHANT_TYPE_CONFIG']['path'] = $path;
  }

  private function parsePage() {
    if (isset($_GET['page']) === false || is_numeric($_GET['page']) === false
      || $_GET['page'] < 1) {
      $GLOBALS['PAGE'] = 1;
      return;
    }
    $GLOBALS['PAGE'] = intval($_GET['page']);
  }

  private function buildMerchantSlideList() {
    if ($GLOBALS['PAGE'] === 1
      && $GLOBALS['MERCHANT_TYPE_CONFIG']['path'] === '/') {
      $GLOBALS['MERCHANT_SLIDE_LIST'] =
        $GLOBALS['HOME_CONFIG']['merchant_slide_list'];
      return;
    }
    $GLOBALS['MERCHANT_SLIDE_LIST'] = DbMerchantSlide::getList(
      $GLOBALS['MERCHANT_TYPE_CONFIG'][0], $GLOBALS['PAGE']
    );
    foreach ($GLOBALS['MERCHANT_SLIDE_LIST'] as &$merchant) {
      $merchant['slide_list'] = explode(' ', $merchant['list']);
    }
  }
}