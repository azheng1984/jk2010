<?php
class AdministratorAccountScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr /><ul>';
    echo '<li>邮箱</li>';
    echo '<li>密码</li>';
    echo '</ul>';
  }
}