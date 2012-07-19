<?php
class PublisherDashboardScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">总计</div><div class="box-content"><div class="block first_block">¥<span class="big">23232.23</span><br />未支付收入</div><div class="block">¥<span class="big">23232.23</span><br />正在付款</div><div class="block">¥<span class="big">23232.23</span><br />活跃订单佣金</div></div>';
    echo '</div>';
    echo '<div class="box"><div class="title">今日</div><div class="box-content"><div class="block first_block"><span class="big">2322233</span><br />流量</div><div class="block"><span class="big">233322</span><br />订单数量</div><div class="block">¥<span class="big">23232.23</span><br />订单支付金额</div>';
    echo '<div class="block">¥<span class="big">23232.23</span><br />活跃订单佣金</div></div>';
    echo '</div>';
  }

  protected function getTitle() {
    return '广告发布商';
  }
}