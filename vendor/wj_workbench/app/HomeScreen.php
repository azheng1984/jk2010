<?php
class HomeScreen extends Screen {
  protected function renderBodyContent() {
    echo 'Welcome!';
  }

  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    echo '<link type="text/css" href="/css/screen.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }
}