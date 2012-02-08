<?php
class NotFoundScreen extends ErrorScreen {
  protected function renderMessage() {
    echo '404 Not Found';
  }
}