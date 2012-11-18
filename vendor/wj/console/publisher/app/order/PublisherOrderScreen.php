<?php
class PublisherOrderScreen extends PublisherScreen {
  public function __construct() {
    if (isset($_COOKIE['session_id']) === false) {
      header('HTTP/1.1 302 Found');
      header('Location: /');
      $this->stop();
    }
  }

  protected function renderHtmlHeadContent() {
    echo '<title>广告发布商 / 效果报告 - 货比万家</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<h1><a href="/">广告发布商</a></h1>';
    echo '<div id="toolbar">用户名 | publisher_id：xxx | <a href="sign_out">退出</a></div>';
    PublisherNavigationScreen::render('performance_report');
    echo '<h2>订单</h2>';
    $this->renderFilter();
    $this->renderGroupBy();
    $this->renderResult();
    $this->renderAverage();
    $this->renderTotal();
    $this->renderFooter();
  }

  private function renderFilter() {
    echo '<h3>过滤</h3>';
    echo '<ul>';
    echo '<li>日期：[]-[]</li>';
    echo '<li>状态：全部 | 已完成 | 未完成</li>';
    echo '<li>流量类型：全部 | 货比万家 | 商家</li>';
    echo '</ul>';
    echo '<hr />';
    echo '<ul>';
    echo '<li>订单编号：[] （商家：[]）[确定]</li>';
    echo '<li>跟踪编号：[]</li>';
    echo '</ul>';
  }

  private function renderGroupBy() {
    echo '<h3>分组</h3>';

    echo '<ul>';
    echo '<li>日期：日 | 月 | 年</li>';
    echo '</ul>';
  }

  private function renderResult() {
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<td>日期</td>';
    echo '<td>订单数量</td>';
    echo '<td>订单交易金额</td>';
    echo '<td>订单佣金</td>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    echo '</tbody>';
    echo '</table>';
  }

  private function renderTotal() {
    echo '<h3>总计</h3>';
    echo '<ul>';
    echo '<li>日期：-</li>';
    echo '<li>订单数量：</li>';
    echo '<li>订单交易金额：</li>';
    echo '<li>订单佣金：</li>';
    echo '</ul>';
  }

  private function renderAverage() {
    echo '<h3>平均</h3>';
    echo '<ul>';
    echo '<li>日期：-</li>';
    echo '<li>订单数量：</li>';
    echo '<li>订单交易金额：</li>';
    echo '<li>订单佣金：</li>';
    echo '</ul>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}