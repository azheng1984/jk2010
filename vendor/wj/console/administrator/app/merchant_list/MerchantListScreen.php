<?php
class MerchantListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr /><div><input /><input type="submit" value="搜索" /></div>';
    echo '图标 名称 今日导出流量（IP） 活跃订单佣金 未支付佣金';
    echo '（通过黄色背景表示已经帐号被暂停）';
  }
}