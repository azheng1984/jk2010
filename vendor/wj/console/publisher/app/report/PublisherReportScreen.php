<?php
class PublisherReportScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr /><ul>';
    echo '<li>单位：日/周/月/年 | 日期区间 | 导入类型（网站/自定义渠道）</li>';
    echo '<li>单位名称 | 单位流量 | 单位平均佣金（佣金总数/流量） | 单位佣金总数</li>';
    echo '</ul>';
  }
}