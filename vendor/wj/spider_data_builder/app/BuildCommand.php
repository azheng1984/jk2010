<?php
class BuildCommand {
  public function execute() {
    for (;;) {
      $log = Db::getRow('SELECT * FROM `electronic-log`');
      if ($log === false) {
        break;
      }
      //TODO: 按照 category 检查 & 加载所有 property（跨 category 的 property 没有意义） 和 category
      //TODO: 按照 category 抽取 product_amount 前 100 的 key，更新 mva index
      // mav index 是一个无序集合，替换，但不重新排序
      // 删除已经过时的 product
      // 如果 mva 已经更新，重新生成所有相关的 product property（包括 web product property list）
      // 增量更新，尽量减少 sql 数量，方便同步
      //  web product property list 中 key 和 value 的顺序按照商家顺序，和 mva 无关
      // 根据 spider product property 映射中的 is_new = 1 或者 is_updated = 0 来增加删除 property，否则不会修改产品 property_list
      $class = 'Product'.ucfirst(strtolower($log['type'])).'Processor';
      $processor = new $class;
      $processor->execute($log['product_id']);
      Db::delete('`electronic-log`', 'id = ?', $log['id']);
    }
  }
}