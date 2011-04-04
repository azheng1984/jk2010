<?php
class NotFoundScreen {
  public function render() {
    $wrapper = new ScreenWrapper($this, '出错了_甲壳');
    $wrapper->render();
  }

  public function renderContent() {
    echo '<div class="red_title error_content">您访问的页面已经删除，或者暂时无法访问</div>';
  }
}