<?php
class PublisherPaymentScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    $row = Db::getRow("SELECT * FROM payment WHERE user_id = ? AND status = 'unpaid'", 1);
    if ($row === false) {
      $row = array('commission' => '0');
    }
    echo '<div class="box"><div class="title">未付款</div>账户余额: ￥';
    echo $row['commission'];
    echo ' <input type="submit" value="付款" />';
    if ($row['commission'] !== '0') {
      $detailList = Db::getAll('SELECT * FROM payment_detail WHERE user_id = ?');//console
      echo '<ul>';
      foreach ($detailList as $detail) {
        $merchantName = Db::getColumn('SELECT name FROM profile WHERE user_id = ?', $detail['merchant_user_id']);
        echo '<li>', $merchantName, ' | ', $detail['commission'], '</li>';
        $orderList = Db::getAll('SELECT * FROM complete_order WHERE payment_detail_id = ?');
        foreach ($orderList as $order) {
          echo $order['time'], ' - ',
            $order['publisher_channel_id'], ' - ',
            $order['merchant_order_id'], ' - ',
            $order['transaction_amount'], ' - ',
            $order['commission'];
        }
      }
      echo '</ul>';
    }
    echo '<br />level 1 商家 佣金</div>';
    echo '<div class="box"><div class="title">正在付款</div>付款总额: ￥xx <br />level 1 商家 佣金<br />
level 2.1(order) 时间 | 渠道 | 跟踪编号 | 订单编号 | 交易金额 | 佣金<br />
level 2.2(order detail) 商品编号 | 名称 | 单价 | 数量 | 总价 | 佣金';
    echo '</div>';
//     echo '<div class="box"><div class="title">未完成</div> level 1(order) 时间 | 渠道 | 跟踪编号 | 商家 | 订单编号 | 交易金额 | 佣金<br />
// level 2(order detail) 商品编号 | 商品名称 | 单价 | 数量 | 总价 | 佣金<br />';
//     echo '总计：数量 交易金额 佣金</div>';
    echo '<div class="box"><div class="title">已付款</div>';
    echo '<br />level 1 付款日期 | 付款金额';
    echo '<br />level 2 商家 | 佣金<br />历史记录</div>';
  }

  protected function getTitle() {
    return '广告发布商/结算';
  }
}