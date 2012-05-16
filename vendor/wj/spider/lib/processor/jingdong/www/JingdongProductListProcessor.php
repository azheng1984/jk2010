<?php
class JingdongProductListProcessor {
  private $html;
  private $page;
  private $tablePrefix;
  private $categoryId;

  public function execute($tablePrefix, $categoryId, $path, $page = 1) {
    $this->categoryId = $categoryId;
    $this->page = $page;
    $this->tablePrefix = $tablePrefix;
    $result = WebClient::get('www.360buy.com', '/products/'.$path.'.html');
    $this->html = $result['content'];
    $this->parseProductList();
    $this->parseNextPage();
    if ($this->page === 1) {
      $this->parsePropertyList();
    }
  }

  private function parseProductList() {
    preg_match_all(
      "{<div class='p-name'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $this->html,
      $matches
    );
    $merchantProductIdList = $matches[1];
    $saleIndex = ($this->page - 1) * 36;
    foreach ($merchantProductIdList as $merchantProductId) {
      Db::insert('task', array('processor' => 'JingdongProduct',
        'argument_list' => var_export(array(
          $this->tablePrefix,
          $this->categoryId,
          $merchantProductId,
          ++$saleIndex
        ), true)
      ));
    }
  }

  private function parseNextPage() {
    preg_match(
      '{class="current".*?href="([0-9-]+).html"}',
      $this->html,
      $matches
    );
    if (count($matches) > 0) {
      $page = $this->page + 1;
      $path = $matches[1];
      Db::insert('task', array('processor' => 'JingdongProductList',
        'argument_list' => var_export(array(
          $this->tablePrefix, $this->categoryId, $path, $page
        ), true)
      ));
    }
  }

  private function parsePropertyList() {
    preg_match(
      '{<div id="select" [\s|\S]*<!--select end -->}', $this->html, $matches
    );
    if (count($matches) > 0) {
      $section = iconv('gbk', 'utf-8', $matches[0]);
      preg_match_all('{<dl.*?</dl>}', $section, $matches);
      foreach ($matches[0] as $item) {
        preg_match_all(
          "{<dt>(.*?)：</dt>}", $item, $matches
        );
        $keyName = $matches[1][0];
        if ($keyName === '价格') {
          continue;
        }
        preg_match_all(
          "{<a.*?href='(.*?).html'.*?>(.*?)</a>}", $item, $matches
        );
        $valueLinkList = $matches[1];
        $valueList = $matches[2];
        $valueAmount = count($valueList);
        $keyId = Db::bind('`'.$this->tablePrefix.'-property_key`', array(
          'category_id' => $this->categoryId, 'name' => $keyName
        ));
        for ($index = 0; $index < $valueAmount; ++$index) {
          $valueName = $valueList[$index];
          if ($valueName === '全部' || $valueName === '其它'
            || $valueName === '不限') {
            continue;
          }
          $valueId = Db::bind('`'.$this->tablePrefix.'-property_value`', array(
            'key_id' => $keyId, 'name' => $valueList[$index]
          ));
          $path = $valueLinkList[$index];
          Db::insert('task', array('processor' => 'JingdongPropertyProductList',
            'argument_list' =>var_export(array(
              $this->tablePrefix, $valueId, $path
            ), true)
          ));
        }
      }
    }
  }
}