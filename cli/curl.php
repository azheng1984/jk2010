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
$connection = new PDO(
  "mysql:host=localhost;dbname=source_360buy",
  "root",
  "a841107!",
  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
require 'WebClient.php';
require 'Cateogry.php';
$client = new WebClient;
$category = new Category;
foreach ($startPoint['www.360buy.com'] as $key => $url) {
  $rootCategroyId = $category->getOrNewId($key);
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
    preg_match_all('{&gt;&nbsp;<a .*?www.360buy.com.*?">(.*?)</a>}', $html, $matches);
    $categoryId = $rootCategroyId;
    $amount = count($matches[1]);
    for ($index = 1; $index < $amount; ++$index) {
      $categoryName = iconv('gbk', 'utf-8', $matches[1][$index]);
      $stat = $connection->prepare("select * from category where `name` = '$categoryName' and parent_id=$categoryId");
      $stat->execute();
      $result = $stat->fetch(PDO::FETCH_ASSOC);
      if ($result === false) {
        $stat = $connection->prepare("insert into category(`name`, parent_id) values('$categoryName',$categoryId)");
        $stat->execute();
        $categoryId = $connection->lastInsertId();
      } else {
        $categoryId = $result['id'];
      }
    }
    $sql = "select * from `list` where category_id=$categoryId and property is null and page=1";
    $stat = $connection->prepare($sql);
    $stat->execute();
    $result = $stat->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
      $sql = "insert into `list`(page, category_id, raw_content) values(1, $categoryId, ?)";
      $stat = $connection->prepare($sql);
      $stat->execute(array(gzcompress($html)));
    }
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

      $sql = "select * from product where id=$id";
      $stat = $connection->prepare($sql);
      $stat->execute();
      $result = $stat->fetch(PDO::FETCH_ASSOC);
      if ($result === false) {
        $sql = "insert into product(id, `title`, category_id, raw_content) values($id, ?, $categoryId, ?)";
        $stat = $connection->prepare($sql);
        $stat->execute(array(iconv('gbk', 'utf-8', $matches[1][0]), gzcompress($html)));
      }
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