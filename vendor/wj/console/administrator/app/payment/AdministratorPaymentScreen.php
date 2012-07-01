<?php
class AdministratorPaymentScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />【收款】<br /><input type="submit" value="已收款" /><br />【付款】<br /><input type="submit" value="已付款" />';
  }
}