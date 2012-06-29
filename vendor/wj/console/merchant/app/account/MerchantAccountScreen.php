<?php
class MerchantAccountScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '<li>邮箱</li>';
    echo '<li>密码</li>';
    echo '<li>描述</li>';
    echo '<li>图标</li>';
    echo '</ul>';
  }
}