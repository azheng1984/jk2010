<?php
class AdministratorListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />[活跃 | 不活跃 | 未激活 | 已停用]（只有 root 可以查看 未激活/已停用）<br />姓名 头像 邮箱 电话 IM（类似产品列表，在单元格内放所有信息）';
  }
}