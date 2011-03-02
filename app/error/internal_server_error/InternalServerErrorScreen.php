<?php
class InternalServerErrorScreen {
  public function render() {
    if (!isset($_ENV['exception'])) {
      throw new NotFoundException;
    }
    $wrapper = new ScreenWrapper($this, '出错了_甲壳');
    $wrapper->render();
  }

  public function renderContent() {
    echo '<div class="red_title error_content">服务器出现异常，请稍候访问</div>';
  }
}