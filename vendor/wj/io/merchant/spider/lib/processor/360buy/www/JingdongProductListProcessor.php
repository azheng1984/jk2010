<?php
class JingdongProductListProcessor {
  private $html;
  private $page;

  public function execute($path) {
    $status = 200;
    try {
      $result = WebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->html = $result['content'];
      $this->page = $this->getPage($path);
      $this->parseProductList();
      $this->parseNextPage();
      if ($this->page === 1) {
        $this->parsePropertyList();
      }
    } catch (Exception $exception) {
      $status = $exception->getCode();
    }
    $this->bindHistory($path, $status);
  }

  private function getPage($path) {
    $list = explode('-', $path);
    if ($list === 3) {
      return 1;
    }
    return intval(end($list));
  }

  private function parseProductList() {
    preg_match_all(
      "{<div class='p-name'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $this->html,
      $matches
    );
    $productIdList = $matches[1];
    if (count($productIdList) === 0) {
      throw new Exception(null, 500);
    }
    $index = ($this->page - 1) * 36;
    foreach ($productIdList as $productId) {
      Db::bind('product_index',
        array('merchant_product_id' => $productId),
        array('index' => $index, 'version' => SPIDER_VERSION)
      );
      $productProcessor = new JingdongProductProcessor;
      $productProcessor->execute($productId);
    }
  }

  private function parseNextPage() {
    preg_match(
      '{class="current".*?href="([0-9-]+).html"}',
      $this->html,
      $matches
    );
    if (count($matches) > 0) {
      $productListProcessor = new JingdongProductListProcessor;
      $productListProcessor->execute($matches[1]);
    }
  }

  private function parsePropertyList() {
    preg_match(
      '{<div id="select" [\s|\S]*<!--select end -->}', $this->html, $matches
    );
    if (count($matches) === 0) {
      return;
    }
    $section = iconv('gbk', 'utf-8', $matches[0]);
    preg_match_all('{<dl.*?</dl>}', $section, $matches);
    $keyIndex = 0;
    foreach ($matches[0] as $item) {
      preg_match_all(
      "{<dt>(.*?)：</dt>}", $item, $matches
      );
      $keyName = $matches[1][0];
      preg_match_all(
        "{<a.*?href='(.*?).html'.*?>(.*?)</a>}", $item, $matches
      );
      $valueLinkList = $matches[1];
      $valueList = $matches[2];
      $valueAmount = count($valueList);
      $keyId = Db::bind('`'.$this->tablePrefix.'-property_key`', array(
        'category_id' => $this->categoryId, 'name' => $keyName
      ), array('`index`' => $keyIndex));
      for ($index = 0; $index < $valueAmount; ++$index) {
        $valueName = $valueList[$index];
        if ($valueName === '全部' || $valueName === '其它'
            || $valueName === '不限') {
          continue;
        }
        $valueId = Db::bind('`'.$this->tablePrefix.'-property_value`', array(
            'key_id' => $keyId, 'name' => $valueList[$index]
        ), array('`index`' => $index));
        $path = $valueLinkList[$index];
        //jingdong property product list
        $processor = new JingdongPropertyProductListProcessor;
        $processor->execute($path);
        Db::insert('task', array('processor' => 'JingdongPropertyProductList',
          'argument_list' =>var_export(array(
          $this->tablePrefix, $valueId, $path
        ), true)
        ));
      }
    }
  }

  private function bindHistory($path, $status) {
    $replacementColumnList = array(
      'status' => $status,
      'version' => SPIDER_VERSION,
    );
    if ($status === 200) {
      $replacementColumnList['last_ok_date'] = date('Y-m-d');
    }
    Db::bind('history', array(
      'processor' => 'ProductList', 'path' => $path,
    ), $replacementColumnList);
  }
}