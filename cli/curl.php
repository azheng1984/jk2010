<?php
require 'WebClient.php';
$client = new WebClient;
$rule = require 'rule.php';
$result = $client->execute('www.360buy.com', '/products/1315-1345-1370-0-0-0-0-0-0-0-1-5-1.html');
$html = iconv('gb2312', 'utf-8', $result['content']);
$matches = array();
preg_match_all(
  '#<li><a href=http://www.360buy.com/products/([\s|\S]*).html>([\s|\S]*)</a></li>#U',
  $html,
  $matches
);
$count = count($matches[1]);
$lists = array();
for ($index = 0; $index < $count; ++$index) {
  $list[$matches[2][$index]] = $matches[1][$index];
}
//print_r($list);
$productIds = array();
foreach ($list as $id) {
  $result = $client->execute('www.360buy.com', '/products/'.$id.'.html');
  $html = iconv('gb2312', 'utf-8', $result['content']);
  preg_match_all(
    "#<div class='p-img'><a target='_blank' href='http://www.360buy.com/product/([0-9]+).html'>#",
    $html,
    $matches
  );
  foreach ($matches[1] as $id) {
    $result = $client->execute('www.360buy.com', '/product/'.$id.'.html');
    $html = iconv('gb2312', 'utf-8', $result['content']);
    preg_match_all(
      "#<h1>.*?</h1>#",
      $html,
      $matches
    );
    echo $matches[0][0]."\n";
  }
  preg_match(
    '#<a href="([0-9-]+).html" class="next">下一页#',
    $html,
    $matches
  );
  break;
}
$client->close();
exit(0);