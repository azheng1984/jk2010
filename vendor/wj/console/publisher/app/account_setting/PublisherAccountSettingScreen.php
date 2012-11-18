<?php
class PublisherAccountSettingScreen extends PublisherScreen {
  public function __construct() {
  }

  protected function renderHtmlHeadContent() {
    echo '<title>广告发布商 / 账户设置 - 货比万家</title>';
  }

  protected function renderHtmlBodyContent() {
    if (isset($_COOKIE['session_id']) === false) {
      $this->renderSignIn();
      return;
    }
    echo '<h1><a href="/">广告发布商</a></h1>';
    echo '<div id="toolbar">用户名 | publisher_id：xxx | <a href="sign_out">退出</a></div>';
    PublisherNavigationScreen::render('home');
    echo '<h2>账户设置</h2>';
    echo '<h3><a href="/account_setting/contact_person">联系人</a></h3>';
    echo '<h3><a href="/account_setting/change_password">修改密码</a></h3>';
    echo '<h3><a href="/account_setting/brand">品牌</a></h3>';
    echo '<p>品牌联合（co-branding）</p>';
    echo '<h3><a href="/account_setting/brand">商家流量统计</a></h3>';
    echo '<p>开启 | 关闭</p>';
    echo '<h3><a href="/account_setting/brand">默认流量类型</a></h3>';
    echo '<p>全部 | 货比万家 | 商家</p>';
    $this->renderFooter();
  }

  private function renderSignIn() {
    echo '<form method="POST" action="/">';
    echo '<div><label for="username">用户名：</label><input id="username" name="username" type="text" /></div>';
    echo '<div><label for="password">密码：</label><input id="password" name="password" type="password" /></div>';
    echo '<div><input type="submit" value="登录" />';
    echo '<a href="/sign_up">注册</a></div>';
    echo '</form>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}