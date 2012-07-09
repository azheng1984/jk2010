<?php
class PublisherReportScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box">';
    echo '<div>时间单位: <span>日</span> <a href="javascript:void(0)">月</a> <a href="javascript:void(0)">年</a></div>';
    echo '<div>时间区间: <input type="text" />-<input type="text" /></div>';
    echo '<div>导入类型: <select><option>全部</option><option>来源网站（只在用户有一个以上网站时显示）</option><option>着陆页（商家列表/商品搜索/其它（包括 404 页面，关于，购物排行榜））</option><option>自定义渠道</option></option></select></div>';
    echo '<table><tr><th>时间</th><th>流量</th><th>活跃订单佣金</th><th>收入</th></tr></table>';
    echo '</div>';
  }

  protected function getTitle() {
    return '广告发布商/效果报表';
  }
}