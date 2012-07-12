<?php
class PublisherReportScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box">';
    echo '<div><br />时间区间: <input type="text" />-<input type="text" /></div>';
    echo '<div>分组: [时间: <b>日</b> <a href="javascript:void(0)">月</a> <a href="javascript:void(0)">年</a>] | 网站 | 自定义渠道</div>';
    echo '<br /><table><tr><th>时间</th><th>流量</th><th>订单数量</th><th>订单支付金额</th><th>活跃订单佣金</th><th>CPC</th><th>完成订单佣金</th></tr><tr><td colspan="7">空</td></tr></table>';
    echo '</div>';
  }

  protected function getTitle() {
    return '广告发布商/效果报表';
  }
}