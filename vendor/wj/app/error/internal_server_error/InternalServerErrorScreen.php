<?php
class InternalServerErrorScreen extends ErrorScreen {
  protected function renderMessage() {
    echo '500 Internal Server Error';
  }
}