<?php
class ProductListProcessor {
  private $html;
  private $name;
  private $page;
  private $tablePrefix;
  private $categoryId;

  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/products/'.$arguments['path'].'.html'
    );
    $this->page = $arguments['page'];
    $this->tablePrefix = $arguments['table_prefix'];
    $this->html = $result['content'];
    if ($this->html === false) {
      return $result;
    }
    $this->name = $arguments['name'];
    $this->parseBreadcrumb($arguments);
    $this->parseProductList();
    $this->parseNextPage();
    if ($this->page === 1) {
      $this->parsePropertyList();
    }
  }

  private function parseBreadcrumb($arguments) {
    if (isset($arguments['category_id'])) {
      $this->categoryId = $arguments['category_id'];
      return;
    }
    preg_match_all(
      '{&gt;&nbsp;<a .*?www.360buy.com.*?">(.*?)</a>}', $this->html, $matches
    );
    $this->categoryId = $arguments['root_category_id'];
    $amount = count($matches[1]);
    for ($index = 1; $index < $amount; ++$index) {
      $categoryName = iconv('gbk', 'utf-8', $matches[1][$index]);
      $this->categoryId = DbId::get('category', array(
        'name' => $categoryName, 'parent_id' => $this->categoryId
      ));
    }
  }

  private function parseProductList() {
    preg_match_all(
      "{<div class='p-name'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $this->html,
      $matches
    );
    $productIds = $matches[1];
    $saleIndex = ($this->page - 1) * 36;
    foreach ($productIds as $id) {
      Db::insert('task', array('type' => 'Product',
        'argument_list' => var_export(array(
          'table_prefix' => $this->tablePrefix,
          'category_id' => $this->categoryId,
          'sale_index' => ++$saleIndex,
          'id' => $id
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
      Db::insert('task', array('type' => 'ProductList',
        'argument_list' => var_export(array(
          'path' => $matches[1],
          'category_id' => $this->categoryId,
          'table_prefix' => $this->tablePrefix,
          'name' => $this->name,
          'page' => $page
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
      preg_match_all(
        '{<dl.*?</dl>}', $section, $matches
      );
      foreach ($matches[0] as $item) {
        preg_match_all(
          "{<dt>(.*?)：</dt>}", $item, $matches
        );
        $key = $matches[1][0];
        if ($key === '价格') {
          continue;
        }
        preg_match_all(
          "{<a.*?href='(.*?).html'.*?>(.*?)</a>}", $item, $matches
        );
        $valueLinkList = $matches[1];
        $valueList = $matches[2];
        $valueAmount = count($valueList);
        //$keyId = DbProperty::getOrNewKeyId($this->tablePrefix, $key);
        for ($index = 0; $index < $valueAmount; ++$index) {
          $value = $valueList[$index];
          if ($value === '全部' || $value === '其它' || $value === '不限') {
            continue;
          }
//           $valueId = DbProperty::getOrNewValueId(
//             $this->tablePrefix, $keyId, $valueList[$index]
//           );
          Db::insert('task', array('task' => 'PropertyProductList',
            'argument_list' =>var_export(array(
              'path' => $valueLinkList[$index],
              'table_prefix' => $this->tablePrefix,
             //'value_id' => $valueId,
              'page' => 1
            ), true)
          ));
        }
      }
    }
  }
}