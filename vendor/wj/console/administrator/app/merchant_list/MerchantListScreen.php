<?php
class MerchantListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<br />[活跃 | 不活跃 | 未激活 | 禁用]<div><input /><input type="submit" value="搜索" /></div>';
    echo '图标 名称 今日 总计（和 merchant dashboard 保持一致）';
    echo '（通过黄色背景表示已经帐号被暂停）';
  }

  protected function getTitle() {
    return '管理员/商家';
  }
}