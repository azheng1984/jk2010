<?php
class MerchantAccountScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '<li>联系人（姓名 邮箱 电话） 密码 网站（名称 图标 描述）</li>';
    echo '</ul>';
  }
}