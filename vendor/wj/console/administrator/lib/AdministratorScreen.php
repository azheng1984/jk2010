<?php
class AdministratorScreen extends ConsoleScreen {
	/* (non-PHPdoc)
 * @see ConsoleScreen::renderConsoleContent()
 */protected function renderConsoleContent() {
    echo '<ul>',
      '<li><span style="background:red;color:#fff;padding:2px 5px">首页</span></li>',
      '<li><a href="/publisher_list">广告发布商</a></li>',
      '<li><a href="/merchant_list">商家<a></li>',
      '<li><a href="/administrator_list">管理员</a>（只有 root 用户才有）</li>',
      '<li><a href="/account">帐户设置</a></li>';
    echo '</ul>';
  }
}