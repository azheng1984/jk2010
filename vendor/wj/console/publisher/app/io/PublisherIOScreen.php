<?php
class PublisherIOScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr /><ul>';
    echo '<li>API: 效果报告，结算（地址 & 交互式文档）</li>';
    echo '<li>回调: 活跃订单更新（文档）</li>';
    echo '<li>API（密码），回调（地址，密码）</li>';
    echo '<li>状态（最后一次更新）</li>';
    echo '</ul>';
  }
}