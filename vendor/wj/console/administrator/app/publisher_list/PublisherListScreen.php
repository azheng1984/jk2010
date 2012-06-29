<?php
class PublisherListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr /><div><input /><input type="submit" value="搜索" /></div>';
    echo '<table><tr><td>logo</td><td>名称[^]</td><td>今日导入流量（IP）[^]</td><td>活跃订单佣金[^]</td><td>未支付佣金[^]</td></tr>',
      '<tr><td><a href="/">LOGO</a></td><td><a href="/">360buy</a></td><td>32323</td><td>￥2332</td><td>￥3233</td></tr>',
      '</table>';
    echo '<div>1 <a href="/merchant/2">2</a> 3 4</div>';
    echo '通过黄色背景表示已经帐号被暂停';
  }
}