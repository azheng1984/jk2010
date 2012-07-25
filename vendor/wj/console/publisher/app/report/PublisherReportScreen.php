<?php
class PublisherReportScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    $channelList = Db::getAll('SELECT id, name FROM channel WHERE user_id = ?', 1);
    echo '<div class="box">',
      '<div><br />过滤：',
      '<form method="GET">时间区间: ';
    if (isset($_GET['group_by'])) {
      echo '<input name="group_by" type="hidden" value="', $_GET['group_by'], '" />';
    }
    if (isset($_GET['channel_id'])) {
      echo '<input name="channel_id" type="hidden" value="', $_GET['channel_id'], '" />';
    }
    echo '<input name="start_date" type="text" value="', date('Y-n-1'), '" />',
      '-<input name="end_date" type="text" value="', date('Y-n-j'), '" />',
      '</div><input type="submit" value="确定" /></form><div>渠道</div>';
    echo '<ul>';
    foreach ($channelList as $channel) {
      if (isset($_GET['channel_id']) && $_GET['channel_id'] === $channel['id']) {
        echo '<b>', $channel['name'], '</b> <a href="?">X</a>';
        continue;
      }
      echo '<li><a href="?channel_id=', $channel['id'], '">',
        $channel['name'], '</a></li>';
    }
    echo '</ul>';
    echo '<div>分组: [时间: <a href="?group_by=day">日</a> <a href="?group_by=month">月</a> <a href="?group_by=year">年</a>] | <a href="?group_by=channel">渠道</a></div>';
    echo '<br /><table><tr><th>时间/渠道</th><th>流量</th><th>订单数量</th><th>订单支付金额</th><th>活跃订单佣金</th><th>CPC</th><th>完成订单佣金</th></tr><tr><td colspan="7">空</td></tr></table>';
    echo '</div>';
  }

  protected function getTitle() {
    return '广告发布商/效果报表';
  }
}