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
      $href = $prefix;
      if ($href === '?') {
        $href = 'report';
      }
      echo '<a href="', $href, '">日</a>';
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
    echo '<br /><table><tr><th>时间/渠道</th><th>流量</th><th>订单数量</th><th>订单交易金额</th><th>活跃订单佣金</th><th>完成订单佣金</th><th>CPC</th></tr>';
    $list = $this->getReport();
    foreach ($list as $row) {
      echo '<tr><td>'.$row['date'].'</td><td>'.$row['traffic'].'</td></tr>';
    }
    $total = $this->getReportTotal();
    $total['COUNT(*)'] = 1;//TODO
    echo '<tr><td>平均</td><td>'.$total['SUM(traffic)']/$total['COUNT(*)'].'</td></tr>';
    echo '<tr><td>总计</td><td>'.$total['SUM(traffic)'].'</td></tr>';
    echo '</table>';
    echo '</div>';
  }

  private function getReport() {
    $select = 'traffic, order_amount, order_transaction_amount, active_order_commission, complete_order_commission';
    $sql = 'SELECT '.$select.' FROM ';
    if (isset($_GET['channel_id'])
      || (isset($_GET['group_by']) && $_GET['group_by'] === 'channel')) {
      $sql .= 'performance_report_by_channel';
    } else {
      $sql .= 'performance_report';
    }
    $sql .= ' WHERE user_id = 1 AND ';
    $whereList = array();
    if (isset($_GET['channel_id'])) {
      $whereList[] = ' `channel_id` = '.$_GET['channel_id'];
    }
    if (isset($_GET['start_date']) === false) {
      $_GET['start_date'] = date('Y-n-1');
    }
    $whereList[] = ' `date` >= '.$_GET['start_date'];
    if (isset($_GET['end_date']) === false) {
      $_GET['end_date'] = date('Y-n-j');
    }
    $whereList[] = ' `date` <= '.$_GET['end_date'];
    $sql .= implode(' AND ', $whereList);
    if (isset($_GET['group_by'])) {
      $groupBy = $_GET['group_by'];
      if ($groupBy === 'channel') {
        $groupBy = 'channel_id';
      }
      $sql .= ' GROUP BY '.$groupBy;
    }
    if (isset($_GET['order_by'])) {
      $sql .= ' ORDER BY '.$_GET['order_by'];
    }
    if (isset($_GET['page'])) {
      $start = ($_GET['page'] - 1) * 50;
      $sql .= ' LIMIT '.$start.', 50';
    } else {
      $sql .= ' LIMIT 0, 50';
    }
    return Db::getAll($sql);
  }

  private function getReportTotal() {
    $select = 'SUM(traffic), SUM(order_amount), SUM(order_transaction_amount), '
      .'SUM(active_order_commission), SUM(complete_order_commission), COUNT(*)';
    $sql = 'SELECT '.$select.' FROM ';
    if (isset($_GET['channel_id'])) {
      $sql .= 'performance_report_by_channel';
    } else {
      $sql .= 'performance_report';
    }
    $sql .= ' WHERE user_id = 1 AND ';
    $whereList = array();
    if (isset($_GET['channel_id'])) {
      $whereList[] = ' `channel_id` = '.$_GET['channel_id'];
    }
    if (isset($_GET['start_date']) === false) {
      $_GET['start_date'] = date('Y-n-1');
    }
    $whereList[] = ' `date` >= '.$_GET['start_date'];
    if (isset($_GET['end_date']) === false) {
      $_GET['end_date'] = date('Y-n-j');
    }
    $whereList[] = ' `date` <= '.$_GET['end_date'];
    $sql .= implode(' AND ', $whereList);
    return Db::getRow($sql);
  }

  protected function getTitle() {
    return '广告发布商/效果报表';
  }
}