<?php
class AdministratorAccountScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr /><ul>';
    echo '<li>姓名 头像 邮箱 电话 IM（qq, skype, msn） 密码</li>';
    echo '</ul>';
  }

  protected function getTitle() {
    return '管理员/账户设置';
  }
}