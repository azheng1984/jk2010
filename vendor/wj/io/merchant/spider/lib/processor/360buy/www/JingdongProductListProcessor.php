<?php
class JingdongProductListProcessor {
  private $html;
  private $page;
  private $categoryId;

  public function execute($path) {
    $status = 200;
    try {
      $result = WebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->html = $result['content'];
      $this->page = $this->getPage($path);
      $this->categoryId = $this->getCategoryId();
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

  private function getCategoryId() {
    preg_match(
      '{<div class="breadcrumb">([\s|\S]*)</a></span>}', $this->html, $matches
    );
    if (count($matches[1]) === 0) {
      throw new Exception(null, 500);
    }
    $categoryName = iconv('gbk', 'utf-8', end(explode('>', $matches[1][0])));
    $id = null;
    Db::bind('category', array('name' => $categoryName), null, $id);
    return $id;
  }

  private function getPage($path) {
    $list = explode('-', $path);
    if (count($list) === 3) {
      return 1;
    }
    return intval(end($list));
  }

  private function parseProductList() {
    preg_match('{id="plist"([\s|\S]*)<!--plist end-->}', $this->html, $matches);
    if (count($matches[1]) === 0) {
      throw new Exception(null, 500);
    }
    $list = array_shift(explode('<li', $matches[1]));
    if (count($list) < 2) {
      throw new Exception(null, 500);
    }
    $index = ($this->page - 1) * 36;
    foreach ($list as $item) {
      preg_match(
        "{img onerror=\"this.src='"
          ."http://www.360buy.com/images/none/none_150.gif'\" src2='(.*?)'}",
        item, $matches
      );
      if (count($matches[1]) === 0) {
        throw new Exception(null, 500);
      }
      $imageSrc = str_replace(
        '360buyimg.com/n2/', '360buyimg.com/n1/', $matches[1][0]
      );
      preg_match(
        "{<div class='p-name'><a target='_blank'"
          ." href='http://www.360buy.com/product/([0-9]+).html'>(.*?)<}",
        item, $matches
      );
      if (count($matches[1]) === 0 || count($matches[2]) === 0) {
        throw new Exception(null, 500);
      }
      $merchantProductId = $matches[1];
      $productTitle = iconv('gbk', 'utf-8', $matches[2]);
      $this->bindProduct($merchantProductId, $productTitle, $imageSrc, $index);
      ++$index;
    }
  }

  private function bindProduct(
    $merchantProductId, $productTitle, $imageSrc, $index
  ) {
    //bind product
    //add image checker
    //add price checker
  }

  private function parseNextPage() {
    preg_match(
      '{href="([0-9-]+).html.*?class="next"}', $this->html, $matches
    );
    if (count($matches) > 0) {
      self::execute($matches[1]);
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
      if ($keyName === '价格' || $keyName === '类别') {
        continue;
      }
      preg_match_all(
        "{<a.*?href='(.*?).html'.*?>(.*?)</a>}", $item, $matches
      );
      $valueLinkList = $matches[1];
      $valueList = $matches[2];
      $valueAmount = count($valueList);
      $keyId = Db::bind('`property_key`', array(
        'category_id' => $this->categoryId, 'name' => $keyName
      ), array('`index`' => $keyIndex));
      for ($index = 0; $index < $valueAmount; ++$index) {
        $valueName = $valueList[$index];
        if ($valueName === '全部' || $valueName === '其它'
          || $valueName === '其它'.$keyName || $valueName === '不限') {
          continue;
        }
        $valueId = Db::bind('`property_value`', array(
          'key_id' => $keyId, 'name' => $valueList[$index]
        ), array('`index`' => $index));
        $path = $valueLinkList[$index];
        $processor = new JingdongPropertyProductListProcessor;
        $processor->execute($path);
      }
      ++$keyIndex;
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