<?php
class PublisherIOScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr /><ul>';
    echo '<li>API: 订单，结算，活跃订单，效果报告（地址 & 调用/返回 格式文档）</li>';
    echo '<li>回调: 订单（格式说明）</li>';
    echo '<li>API（密码），回调（地址，密码）</li>';
    echo '<li>状态（最新更新）</li>';
    echo '</ul>';
  }
}