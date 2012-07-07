<?php
class PublisherAccountScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '联系人（姓名 邮箱 电话） 密码 收款帐户（开户行，户名，帐号） 网站（名称，图标，域名）';
  }
}