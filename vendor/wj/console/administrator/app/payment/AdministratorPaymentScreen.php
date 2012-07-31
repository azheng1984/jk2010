<?php
class AdministratorPaymentScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '（只有 “财务” 才可以确认收款和付款，30天过期时间，过期需要重新申请 对帐/付款）';
    echo '<br />【收款】';
    echo '<br />level 1(merchant) 商家 佣金 <input type="submit" value="已收款" /><br />';
    echo 'level 1.1 广告发布商 佣金<br />';
    echo 'level 2(order) 订单编号 | 订单支付金额 | 佣金<br />';
    echo 'level 3(order detail) 商品编号 | 商品名称 | 单价 | 数量 | 总价 | 佣金<br />';
    echo '历史记录 <input type="submit" value="撤销" />';
    echo '<br /><br />【付款】';
    echo '<br />level 1(publisher) 广告发布商 佣金 <input type="submit" value="已付款" /> <input type="submit" value="取消" /><br />';
    echo 'level 1.1 商家 佣金<br />';
    echo 'level 2(order) 渠道 | 跟踪编号 | 订单编号 | 支付金额 | 佣金<br />';
    echo 'level 3(order detail) 商品编号 | 商品名称 | 单价 | 数量 | 总价 | 佣金<br />';
    echo '历史记录 <input type="submit" value="撤销" />';
    
  }

  protected function getTitle() {
    return '管理员/结算';
  }
}