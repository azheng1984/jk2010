<?php
class CategoryListParser {
  private $categoryListUrl = array(
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

  public function execute($client, $category, $name, $url) {
    $categoryId = $category->getOrNewId($name);
    $result = $client->get('www.360buy.com', $url);
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
    return $list;
  }
}