<?php
class AdministratorListScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<br />[活跃 | 不活跃 | 未激活 | 禁用]（只有 root（超级管理员） 可以查看 未激活/已停用，设置权限）<br />姓名 头像 邮箱 电话 IM（类似产品列表，在单元格内放所有信息）';
  }

  protected function getTitle() {
    return '管理员/管理员';
  }
}