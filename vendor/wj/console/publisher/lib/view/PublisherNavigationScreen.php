<?php
class PublisherNavigationScreen {
  private static $config = array(
    '/' => '首页',
    '/performance_report' => '流量',
    '/order' => '订单',
    '/income' => '收入',

    '/payment' => '结算',
    '/io' => '数据接口',
    '/ad' => '广告',
    '/account_setting' => '账户设置',
  );

  public static function render() {
    echo '<ul>';
    foreach (self::$config as $path => $name) {
      echo '<li><a href="'.$path.'">'.$name.'</a></li>';
    }
    echo '</ul>';
  }
}