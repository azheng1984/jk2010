<?php
class PublisherDashboardScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">今天</div><div class="box-content"><div class="block first_block">流量<br /><span class="big">2322233</span></div><div class="block">订单数量<br /><span class="big">233322</span></div><div class="block">订单支付金额<br />¥<span class="big">23232.23</span></div>';
    echo '<div class="block">活跃订单佣金<br />¥<span class="big">23232.23</span></div></div>';
    echo '</div>';
    echo '<div class="box"><div class="title">总计</div><div class="box-content"><div class="block first_block">未支付收入<br />¥<span class="big">23232.23</span></div><div class="block">正在付款<br />¥<span class="big">23232.23</span></div><div class="block">活跃订单佣金<br />¥<span class="big">23232.23</span></div></div>';
    echo '</div>';
  }

  protected function getTitle() {
    return '广告发布商';
  }
}