<?php

class DbQuery {
  public function getList() {
    return array();
  }

  public function get($id) {
    return Db::getRow('SELECT * FROM query WHERE id = ?', $id);
  }
}