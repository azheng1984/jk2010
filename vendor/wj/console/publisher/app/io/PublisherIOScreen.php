<?php
class PublisherIOScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">状态</div>（最后一次更新）</div>';
    echo '<div class="box">API: 效果报告，结算（地址 & 交互式文档）</div>';
    echo '<div class="box">回调: 活跃订单更新（文档）</div>';
    echo '<div class="box"><div class="title">网站</div>（名称，图标，域名，域名 <=> 渠道 映射（类似 adsense 网址渠道））</div>';
  }

  protected function getTitle() {
    return '广告发布商/数据接口';
  }
}