<?php
class PublisherIOScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr /><ul>';
    echo '<li>API: 订单（单个订单），结算，效果报告（地址 & 文档）</li>';
    echo '<li>回调: 订单（文档）</li>';
    echo '<li>API（密码），回调（地址，密码）</li>';
    echo '<li>状态（最后一次更新）</li>';
    echo '</ul>';
  }
}