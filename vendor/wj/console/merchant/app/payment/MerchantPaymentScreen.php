<?php
class MerchantPaymentScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '<li>【未支付佣金】</li>';
    echo 'level 1 <li></li>';
    echo 'level 2 <li></li>';
    echo '<li>【已支付佣金】</li>';
    echo '</ul>';
  }
}