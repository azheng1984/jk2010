<?php
class AdministratorListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />[有效 | 无效 | 新注册]（只有 root 可以查看 无效/新注册 管理员）<br />姓名 头像 邮箱 电话 IM（类似产品列表，在单元格内放所有信息）';
  }
}