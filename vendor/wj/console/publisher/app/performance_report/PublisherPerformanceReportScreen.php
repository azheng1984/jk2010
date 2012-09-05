<?php
class PublisherPerformanceReportScreen extends PublisherScreen {
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
    echo '<div id="toolbar">用户名 | <a href="sign_out">退出</a></div>';
    PublisherNavigationScreen::render('performance_report');
    $this->renderFilter();
    $this->renderResult();
    $this->renderTatal();
  }

  private function renderFilter() {
    
  }

  private function renderResult() {
    echo '<table>';
    echo '</table>';
  }

  private function renderTotal() {
    
  }
}