<?php
class PublisherAccountSettingScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">联系人</div>（姓名 邮箱 电话（可以是多个，包括手机））</div>';
    echo '<div class="box"><div class="title">密码</div><input /></div>';
    echo '<div class="box"><div class="title">收款</div>（开户行，户名，帐号）,（自动付款，代扣税费（历史配置会保存，类似订单收款地址））</div>';
    echo '<div class="box"><div class="title">数据接口</div></div>';
    echo '<div class="box"><div class="title">网站</div>（名称，图标，域名，域名 <=> 渠道 映射（类似 adsense 网址渠道））</div>';
  }

  protected function getTitle() {
    return '广告发布商/账户设置';
  }
}