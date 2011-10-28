<?php
class JingDongInitCommand extends InitCommand {
  protected function getCategoryListLinks() {
    return array(
      'PublicationCategoryList' => array(
        'book.360buy.com' => array(
          '图书' => array('table_prefix' => 'book', 'url' => '/book/booksort.aspx'),
        ),
        'mvd.360buy.com' => array(
          '音乐' => array('table_prefix' => 'music', 'url' => '/mvdsort/4051.html'),
          '影视' => array('table_prefix' => 'movie', 'url' => '/mvdsort/4052.html'),
        ),
      ),
      'CategoryList' => array(
        'www.360buy.com' => array(
          '家用电器、汽车用品' => array('table_prefix' => 'electronic', 'url' => '/electronic.html'),
          '手机数码' => array('table_prefix' => 'digital', 'url' => '/digital.html'),
          '电脑、软件、办公' => array('table_prefix' => 'computer', 'url' => '/computer.html'),
          '家居、厨具、家装' => array('table_prefix' => 'home', 'url' => '/home.html'),
          '服饰鞋帽' => array('table_prefix' => 'clothing', 'url' => '/products/1315-1347-2558-0-0-0-0-0-0-0-1-5-1.html'),
          '个护化妆' => array('table_prefix' => 'beauty', 'url' => '/beauty.html'),
          '钟表首饰、礼品箱包' => array('table_prefix' => 'watch', 'url' => '/watch.html'),
          '运动健康' => array('table_prefix' => 'sports', 'url' => '/sports.html'),
          '母婴、玩具、乐器' => array('table_prefix' => 'baby', 'url' => '/baby.html'),
          '食品饮料、保健品' => array('table_prefix' => 'food', 'url' => '/food.html'),
        ),
      )
    );
  }
}