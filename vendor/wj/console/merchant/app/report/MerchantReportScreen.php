<?php
class MerchantReportScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '<li>单位：日/周/月/年 | 日期区间 | 导出类型（首页/促销/商品）</li>';
    echo '<li>单位名称 | 单位流量 | 单位平均佣金（佣金总数/流量） | 单位佣金总数</li>';
    echo '</ul>';
    // | 渠道[input]
  }
}