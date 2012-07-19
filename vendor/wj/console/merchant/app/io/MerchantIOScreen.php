<?php
class MerchantIOScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<ul>';
    echo '<li>API: 商品（CRUD），促销活动（CRUD），订单（CRU），对帐单（只有通过 管理员 确认的收款帐单，才能把订单状态改为已支付），结算，效果报告（交互式文档）</li>';
    echo '<li>回调: 商品，促销活动，订单（格式说明）</li>';
    echo '<li>配置： API（密码），回调（地址，密码）</li>';
    echo '<li>状态（最后一次更新）</li>';
    echo '<li>促销活动 预览，更新（回调）</li>';
    echo '</ul>';
  }

  protected function getTitle() {
    return '商家/数据接口';
  }
}