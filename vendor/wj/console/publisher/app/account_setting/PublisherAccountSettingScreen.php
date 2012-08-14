<?php
class PublisherAccountSettingScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">联系人</div>（姓名 邮箱 电话（可以是多个，包括手机））</div>';
    echo '<div class="box"><div class="title">密码</div><input /></div>';
    echo '<div class="box"><div class="title">渠道</div>[活跃|闲置][id|名称 筛选]</div>';
    echo '<div class="box"><div class="title">合作伙伴推广</div>网站（名称，图标，域名）</div>';
  }

  protected function getTitle() {
    return '广告发布商/账户设置';
  }
}