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
    $segmentList = Segmentation::execute($name);
    $result = ProductSearch::search($segmentList);
    $amount = $result['total_found'];
    if ($amount !== 0) {
      $pinyin = Pinyin::encode($name, false);
      $alphabetIndex = AlphabetIndex::get($segmentList[0], $pinyin);
      $id = DbWebQuery::insert(
        $categoryId, $alphabetIndex, $name, $pinyin, $amount
      );
      DbSearchQuery::insert($id, $alphabetIndex, $name, $pinyin, $amount);
    }
  }
}