<?php
class SignUpScreen extends Html {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<h2>注册</h2>';
    echo '<form action="/sign_up" method="POST">';
    $this->printLine('<label for="email">邮箱:</label><input id="email" name="email"/>');
    $this->printLine('<label for="password">密码:</label><input id="password" name="password" type="password"/>');
    $this->printLine('<label for="name">昵称:</label><input id="name" name="name"/>');
    $this->printLine('<button type="submit" name="submit">提交</button>');
    //$this->printLine('<label for="location">位置:</label><input id="location" name="location"/>');
    echo '</form>';
  }

  private function printLine($line) {
    echo '<div>', $line, '</div>';
  }
}