<?php
class ShoppingRemoteTask {
  static public function add() {
    //如果无法添加到远程服务器（网络错误/服务器宕机），添加到缓存，等下次调用时一并添加
  }
}