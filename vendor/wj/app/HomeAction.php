<?php
class HomeAction {
  public function GET() {
    $GLOBALS['HOME_CONFIG'] = require CONFIG_PATH.'home.config.php';
    $this->parseMerchantType();
    $this->parsePage();
    $this->buildMerchantList();
  }

  private function parseMerchantType() {
    $path = $GLOBALS['PATH_SECTION_LIST'][1];
    if ($path !== '' && !isset(
      $GLOBALS['HOME_CONFIG']['merchant_type_list'][$path])) {
      throw new NotFoundException;
    }
    if ($path !== '') {
      $merchantTypeList = $GLOBALS['HOME_CONFIG']['merchant_type_list'];
      $GLOBALS['MERCHANT_TYPE'] = $merchantTypeList[$path];
      $GLOBALS['MERCHANT_TYPE']['path'] = $path;
    }
  }

  private function parsePage() {
    if (!isset($_GET['page'])) {
      $GLOBALS['PAGE'] = 1;
      return;
    }
    if (!is_numeric($_GET['page']) || $_GET['page'] < 1) {
      throw new NotFoundException;
    }
    $GLOBALS['PAGE'] = intval($_GET['page']);
  }

  private function buildMerchantList() {
    if ($GLOBALS['PAGE'] === 1 && isset($GLOBALS['MERCHANT_TYPE']) === false) {
      $GLOBALS['MERCHANT_LIST'] = $GLOBALS['HOME_CONFIG']['merchant_list'];
      return;
    }
    $typeId = null;
    if (isset($GLOBALS['MERCHANT_TYPE'])) {
      $typeId = $GLOBALS['MERCHANT_TYPE'][0];
    }
    $start = ($GLOBALS['PAGE'] - 1) * 25;
    $GLOBALS['MERCHANT_LIST'] = DbHomeMerchant::getList($typeId, $start);
    if (count($GLOBALS['MERCHANT_LIST']) === 0) {
      throw new NotFoundException;
    }
  }
}