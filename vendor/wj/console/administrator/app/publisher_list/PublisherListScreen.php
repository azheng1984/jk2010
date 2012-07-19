<?php
class PublisherListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<br />[活跃 | 不活跃 | 未激活 | 已停用]<div><input /><input type="submit" value="搜索" /></div>';
    echo '图标 名称 今日（流量 | 新增活跃订单佣金 | 新增收入）总计（活跃订单佣金 | 未支付收入 | 正在付款）（和 publisher dashboard 保持一致）';
    echo '（通过黄色背景表示已经帐号被暂停）';
  }

  protected function getTitle() {
    return '管理员/广告发布商';
  }
}