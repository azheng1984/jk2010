<?php
class MerchantListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />[有效 | 无效 | 新注册]<div><input /><input type="submit" value="搜索" /></div>';
    echo '图标 名称 今日导出流量（IP） 活跃订单佣金 未支付佣金';
    echo '（通过黄色背景表示已经帐号被暂停）';
    echo '管理员视角的 merchant （就像用列表的形式整合 dashborad，底层 和 大多数展示逻辑 都是可以复用的）';
  }
}