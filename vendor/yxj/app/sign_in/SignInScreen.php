<?php
class SignInScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<h2>登录</h2>';
    echo '<form action="/sign_in" method="POST">';
    $this->printLine('<label for="email">邮箱:</label><input id="email" name="email"/>');
    $this->printLine('<label for="password">密码:</label><input id="password" name="password" type="password"/>');
    $this->printLine('<button type="submit" name="submit">提交</button>');
    echo '</form>';
  }

  private function printLine($line) {
    echo '<div>', $line, '</div>';
  }
}