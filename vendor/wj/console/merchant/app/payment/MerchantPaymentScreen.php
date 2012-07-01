<?php
class MerchantPaymentScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '<li>【未付款】</li>';
    echo 'level 1 <li></li>';
    echo 'level 2 <li><input type="submit" value="付款" /></li>';
    echo '<li>【正在付款】<br />对帐单<br /><input type="submit" value="取消" /></li>';
    echo '<li>【已付款】</li>';
    echo '</ul>';
  }
}