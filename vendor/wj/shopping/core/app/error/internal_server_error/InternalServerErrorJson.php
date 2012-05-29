<?php
class InternalServerErrorJson {
  public function render() {
    header('Content-Type: application/json; charset=utf-8');
  }
}