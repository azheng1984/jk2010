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
    '个护化妆', 'm/beauty.html',
    '钟表首饰、礼品箱包' => '/watch.html',
    '运动健康' => '/sports.html',
    '母婴、玩具、乐器' => '/baby.html',
    '食品饮料、保健品' => '/food.html',
  ),
);

require 'WebClient.php';
$client = new WebClient;
$listIndex = 1;
foreach ($startPoint['www.360buy.com'] as $key => $url) {
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
  for ($index = 0; $index < $count; ++$index) {
    $list[iconv('gb2312', 'utf-8', $matches[2][$index])] = $matches[1][$index];
  }
  $productIds = array();
  foreach ($list as $id) {
    $result = $client->execute('www.360buy.com', '/products/'.$id.'.html');
    $html = $result['content'];
    preg_match_all(
      "{<div class='p-img'><a target='_blank' href='http://www.360buy.com/product/([0-9]+).html'>}",
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
      echo '['.$key.'] '.iconv('gb2312', 'utf-8', $matches[1][0])."\n";
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