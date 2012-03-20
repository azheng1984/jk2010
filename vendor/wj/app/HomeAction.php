<?php
class HomeAction {
  public function GET() {
    $GLOBALS['HOME_CONFIG'] = require CONFIG_PATH.'home.config.php';
    $this->parseMerchantType();
    $this->parsePage();
    $this->parseSlideIndex();
    $this->buildMerchantSlide();
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

  private function parseSlideIndex() {
    if (isset($_GET['slide']) === false || is_numeric($_GET['slide']) === false
        || $_GET['slide'] < 1) {
      $GLOBALS['SLIDE_INDEX'] = 1;
      return;
    }
    $GLOBALS['SLIDE_INDEX'] = intval($_GET['slide']);
  }

  private function buildMerchantSlide() {
    if ($GLOBALS['PAGE'] === 1
      && $GLOBALS['MERCHANT_TYPE_CONFIG']['path'] === '/') {
      $GLOBALS['MERCHANT_SLIDE'] = $GLOBALS['HOME_CONFIG']['merchant_list'];
      return;
    }
    $GLOBALS['MERCHANT_SLIDE'] = DbMerchantSlide::getList(
      $GLOBALS['MERCHANT_TYPE_CONFIG'][0], $GLOBALS['PAGE']
    );
    //TODO:fetch slide into array
  }
}