<?php
class PublisherIOScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box">API: 效果报告，结算（地址 & 交互式文档 & 当前状态）</div>';
    echo '<div class="box">回调: 活跃订单更新（文档）</div>';
  }

  protected function getTitle() {
    return '广告发布商/数据接口';
  }
}