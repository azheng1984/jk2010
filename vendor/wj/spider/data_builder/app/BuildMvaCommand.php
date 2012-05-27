<?php
//TODO: 按照 category 抽取 product_amount 前 100 的 key，更新 mva index
// mav index 是一个无序集合，替换，但不重新排序
class BuildMvaCommand {
  public function execute() {
  }

  private function updateByCategory($category) {
    $currentMvaKeyList = array();
    $currentMvaKeyAmount = count($currentMvaKeyList);
    $mavKeyList = Db::getAll('SELECT * FROM product_key WHERE category_id = ?'.
      ' ORDER BY product_amount LIMIT 100', $category['id']);
    //TODO: 记录 mva index 修改的 key, 为了 search db 中涉及到的 product 做更新
    foreach ($mavKeyList as $item) {
      if ($item['mva_index'] === null) {
        if ($currentMvaKeyAmount < 100) {
          //0-99 寻找 mva 空位
          ++$currentMvaKeyAmount;
          continue;
        }
        //把 pa 最小的 mva_key 换出
      }
    }
    //更新所有涉及到的产品
  }
}