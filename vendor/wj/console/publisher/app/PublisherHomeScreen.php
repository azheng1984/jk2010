<?php
class PublisherHomeScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    $today = Db::getRow(
      'SELECT traffic, order_amount, order_transaction_amount, active_order_commission'
        .' FROM performance_report'
        .' WHERE user_id = ? AND `date` = ?', 1, date('Y-m-d')
    );
    if ($today === false) {
      $today = array(
        'traffic'=> 0,
        'order_amount' => 0,
        'order_payment' => 0,
        'active_order_commission' => 0
      );
    }
    $activeOrderCommission = Db::getColumn(
      'SELECT commission FROM active_order_report WHERE user_id = ?', 1
    );
    if ($activeOrderCommission === false) {
      $activeOrderCommission = 0;
    }
    $unpaidCommission = Db::getColumn(
      'SELECT commission FROM payment'
        ." WHERE user_id = ? AND status = 'UNPAID'", 1
    );
    if ($unpaidCommission === false) {
      $unpaidCommission = 0;
    }
    $processingCommission = Db::getColumn(
      'SELECT commission FROM payment'
        ." WHERE user_id = ? AND status = 'PROCESSING'", 1
    );
    if ($processingCommission === false) {
      $processingCommission = 0;
    }
    echo '<div id="five-column" class="box"><div class="title">今天</div><div class="box-content">',
      '<div class="block first_block">流量<br /><span class="big">', $today['traffic'], '</span></div>',
      '<div class="block">预计订单数量<br /><span class="big">', $today['order_amount'], '</span></div>',
      '<div class="block">预计订单交易金额<br />¥<span class="big">', number_format($today['order_payment'], 2), '</span></div>',
    '<div class="block">预计订单佣金<br />¥<span class="big">', number_format($today['order_payment'], 2), '</span></div>';
    echo '<div class="block">预计 CPC<br />¥<span class="big">', number_format($today['active_order_commission'], 2), '</span></div></div>';
    echo '</div>';
    echo '<div class="box"><div class="title">总计</div><div class="box-content">',
      '<div class="block first_block">账户余额<br />¥<span class="big">', number_format($unpaidCommission, 2), '</span></div>',
      '<div class="block">正在付款<br />¥<span class="big">', number_format($processingCommission, 2), '</span></div>',
      '<div class="block">未完成订单佣金<br />¥<span class="big">', number_format($activeOrderCommission, 2), '</span></div></div>';
    echo '</div>';
  }

  protected function getTitle() {
    return '广告发布商';
  }
}