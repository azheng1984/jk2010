<?php
class JingDongInitCommand extends InitCommand {
  protected function getCategoryListLinks() {
    return array(
      'PublicationCategoryList' => array(
        'book.360buy.com' => array(
          '图书' => '/book/booksort.aspx',
        ),
        'mvd.360buy.com' => array(
          '音乐' => '/mvdsort/4051.html',
          '影视' => '/mvdsort/4052.html',
        ),
      ),
      'CategoryList' => array(
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
      )
    );
  }
}