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
    $queryList = array();
    if(isset($_GET['start_date'])) {
      $queryList[] = 'start_date='.$_GET['start_date'];
    }
    if (isset($_GET['end_date'])) {
      $queryList[] = 'end_date='.$_GET['end_date'];
    }
    if (isset($_GET['group_by']) && $_GET['group_by'] !== 'channel') {
      $queryList['group_by'] = 'group_by='.$_GET['group_by'];
    }
    $prefix = '?'.implode('&', $queryList);
    foreach ($channelList as $channel) {
      if (isset($_GET['channel_id']) && $_GET['channel_id'] === $channel['id']) {
        echo '<b>', $channel['name'], '</b> <a href="', $prefix, '">X</a>';
        continue;
      }
      echo '<li><a href="', $prefix;
      if ($prefix !== '?') {
        echo '&';
      }
      echo 'channel_id=', $channel['id'], '">', $channel['name'], '</a></li>';
    }
    echo '</ul>';
    unset($queryList['group_by']);
    if (isset($_GET['channel_id'])) {
      $queryList['channel_id'] = 'channel_id='.$_GET['channel_id'];
    }
    $prefix = '?'.implode('&', $queryList);
    if ($prefix !== '?') {
      $prefix .= '&';
    }
    echo '<div>分组: [时间:';
    if (isset($_GET['group_by']) === false
      || ($_GET['group_by'] !== 'month' && $_GET['group_by'] !== 'year' && $_GET['group_by'] !== 'channel')) {
      echo '<b>日</b>';
    } else {
      echo '<a href="', $prefix, 'group_by=day">日</a>';
    }
      if (isset($_GET['group_by']) && $_GET['group_by'] === 'month') {
      echo '<b>月</b>';
    } else {
      echo '<a href="', $prefix, 'group_by=month">月</a>';
    }
    if (isset($_GET['group_by']) && $_GET['group_by'] === 'year') {
      echo '<b>年</b>';
    } else {
      echo '<a href="', $prefix, 'group_by=year">年</a>';
    }
    echo ' | ';
    if (isset($_GET['channel_id'])) {
      unset($queryList['channel_id']);
      $prefix = '?'.implode('&', $queryList);
      if ($prefix !== '?') {
        $prefix .= '&';
      }
    }
    if (isset($_GET['group_by']) && $_GET['group_by'] === 'channel') {
      echo '<b>渠道</b>';
    } else {
      echo '<a href="', $prefix, 'group_by=channel">渠道</a>';
    }
    echo '</div>';
    //SELECT FROM performance_report/performance_report_by_channel
    //SELECT sum(),avg() FROM performance_report/performance_report_by_channel
    echo '<br /><table><tr><th>时间/渠道</th><th>流量</th><th>订单数量</th><th>订单支付金额</th><th>活跃订单佣金</th><th>CPC</th><th>完成订单佣金</th></tr><tr><td colspan="7">空</td></tr></table>';
    echo '</div>';
  }

  protected function getTitle() {
    return '广告发布商/效果报表';
  }
}