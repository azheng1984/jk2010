<?php
$startPoint = array(
  'book.360buy.com' => array(
    '图书' => '/book/booksort.aspx',
  ),
  'mvd.360buy.com' => array(
    '/mvdsort/4051.html',
    '/mvdsort/4052.html',
  ),
  'www.360buy.com' => array(
    '家用电器、汽车用品' => '/electronic.html',
    '手机数码' => '/digital.html',
    '电脑、软件、办公' => '/computer.html',
    '家居、厨具、家装' => '/home.html',
    '服饰鞋帽' => '/products/1315-1347-2558-0-0-0-0-0-0-0-1-5-1.html',
    '个护化妆' => '/beauty.html',
    '钟表首饰、礼品箱包' => '/watch.html',
    '运动健康' => '/sports.html',
    '母婴、玩具、乐器' => '/baby.html',
    '食品饮料、保健品' => '/food.html',
  ),
);
require 'WebClient.php';
require 'DbConnection.php';
require 'Db.php';
require 'Category.php';
require 'ProductList.php';
require 'Product.php';
$client = new WebClient;
$category = new Category;
foreach ($startPoint['www.360buy.com'] as $key => $url) {
  $categoryId = $category->getOrNewId($key);
  $result = $client->execute('www.360buy.com', $url);
  $html = $result['content'];
  $matches = array();
  preg_match_all(
    '{<li><a href=http://www.360buy.com/products/(.*?).html>(.*?)</a></li>}',
    $html,
    $matches
  );
  $count = count($matches[1]);
  $list = array();
  for ($index = 1; $index < $count; ++$index) {
    $list[iconv('gbk', 'utf-8', $matches[2][$index])] = $matches[1][$index];
  }
  $productIds = array();
  foreach ($list as $id) {
    $result = $client->execute('www.360buy.com', '/products/'.$id.'.html');
    $html = $result['content'];
    $matches = array();
    preg_match(
      '{<div id="select" [\s|\S]*<!--select end -->}', $html, $matches
    );
    if (count($matches) > 0) {
      $section = $matches[0];
      preg_match_all(
        '{<dl.*?</dl>}', $section, $matches
      );
      print_r($matches);
    }
    exit;
    $matches = array();
    preg_match_all(
      '{&gt;&nbsp;<a .*?www.360buy.com.*?">(.*?)</a>}', $html, $matches
    );
    $amount = count($matches[1]);
    for ($index = 1; $index < $amount; ++$index) {
      $categoryName = iconv('gbk', 'utf-8', $matches[1][$index]);
      $categoryId = $category->getOrNewId($categoryName, $categoryId);
    }
    $productList = new ProductList;
    $productList->insert($categoryId, null, 1, $html);
    preg_match_all(
      "{<div class='p-img'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $html,
      $matches
    );
    foreach ($matches[1] as $id) {
      $result = $client->execute('www.360buy.com', '/product/'.$id.'.html');
      $html = $result['content'];
      
      preg_match_all(
        "{<h1>(.*?)(<font.*?)?</h1>}",
        $html,
        $matches
      );
      $product = new Product;
      $product->insert(
        $id, $categoryId, iconv('gbk', 'utf-8', $matches[1][0]), $html
      );
      echo $matches[1][0]."\n";
      break;
    }
    preg_match(
      '{<a href="([0-9-]+).html" class="next">}',
      $html,
      $matches
    );
    break;
  }
}
$client->close();
exit(0);