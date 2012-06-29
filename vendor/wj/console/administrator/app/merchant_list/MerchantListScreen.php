<?php
class MerchantListScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    echo '商家列表';
    echo '<div><input /><input type="submit" value="搜索" /></div>';
    echo '<table><tr><td>用户名[^]</td><td>流量[^]</td><td>活跃订单[^]</td><td>支出[^]</td></tr>',
      '<tr><td><a href="/">360buy</a></td><td>32323</td><td>233个</td><td>￥23233</td></tr>',
      '</table>';
    echo '<div>1 <a href="/merchant/2">2</a> 3 4</div>';
  }
}