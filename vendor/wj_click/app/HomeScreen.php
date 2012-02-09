<?php
class HomeScreen {
  public function render() {
    //内部不使用 target _blank，只有很少页面（home，search result，index, error, redirect）
    //ip，session id，time，“回头率”，页面访问路径，跳出率
    //内部使用 landing tracking（不需要为每个连接都加点击事件），外部使用 click tracking
    //客户端而不是服务器端
    //0 => 1 => x
    //0 => x
    //0 => 1 | 2
    //0 => 1 => 2
    //0 => 1
    echo 'Welcome!';
  }
}