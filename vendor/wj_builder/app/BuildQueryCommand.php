<?php
class BuildQueryCommand {
  public function execute() {
    foreach (DbWebCategory::getAll() as $webCategory) {
      $this->insert($webCategory['id'], $webCategory['name']);
      foreach (DbWebKey::getList($webCategory['id']) as $webKey) {
        foreach (DbWebValue::getList($webKey['id']) as $webValue) {
          $this->insert(
            $webCategory['id'], $webValue['name'].$webCategory['name']
          );
        }
      }
    }
  }

  private function insert($categoryId, $name) {
    $result = ProductSearch::search(Segmentation::execute($name));
    $amount = $result['total_found'];
    if ($amount !== 0) {
      $id = DbWebQuery::insert($categoryId, $name, $amount);
      DbSearchQuery::insert($id, $name, $amount);
    }
  }
}