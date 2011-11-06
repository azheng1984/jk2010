<?php
class HomeScreen extends Screen {
  public function renderBodyContent() {
    echo '<script>document.getElementById("search_input").focus()</script>';
  }

  public function renderHeadContent() {}
}