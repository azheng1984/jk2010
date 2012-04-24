<?php
class HomeAction {
  public function GET() {
    $GLOBALS['HOME_CONFIG'] = require CONFIG_PATH.'home.config.php';
    PaginationParser::parseGet();
    $this->parseMerchantType();
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

  private function buildSlideshow() {
    if ($GLOBALS['PAGE'] === 1 && $GLOBALS['MERCHANT_TYPE']['path'] === '/') {
      $GLOBALS['SLIDESHOW'] = $GLOBALS['HOME_CONFIG']['slideshow'];
      return;
    }
    $GLOBALS['SLIDESHOW'] = array();
    foreach (self::getMerchantSlideList() as $item) {
      $item['slide_list'] = explode(' ', $item['list']);
      $GLOBALS['SLIDESHOW'][$item['id']] = $item;
    }
  }

  private function getMerchantSlideList() {
    $offset = ($GLOBALS['PAGE'] - 1) * 5;
    $sql = 'SELECT * FROM merchant_slide';
    $sqlSuffix = ' ORDER BY id LIMIT '.$offset.', 5';
    $typeId = $GLOBALS['MERCHANT_TYPE'][0];
    if ($typeId !== null) {
      return Db::getAll($sql.' WHERE type_id = ?'.$sqlSuffix, $typeId);
    }
    return Db::getAll($sql.$sqlSuffix);
  }
}