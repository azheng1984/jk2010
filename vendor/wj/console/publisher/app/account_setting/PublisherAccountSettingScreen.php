<?php
class PublisherAccountSettingScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">联系人</div>（姓名 邮箱 电话（可以是多个，包括手机））</div>';
    echo '<div class="box"><div class="title">密码</div><input /></div>';
  }

  protected function getTitle() {
    return '广告发布商/账户设置';
  }
}