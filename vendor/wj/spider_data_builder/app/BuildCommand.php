<?php
class BuildCommand {
  private $keyMapper;
  private $valueMapper;

  public function execute() {
    //sync meta
    $categoryList = Db::getAll('SELECT * FROM category');//spider
    foreach ($categoryList as $category) {
      $this->syncByCategory($category);
    }
    //sync product
    $this->syncProductByCategory($category);
  }

  private function syncByCategory($category) {
    $this->mvaIndexList = null;
    $categoryId = null;//TODO bind builder category
    $keyList = Db::getAll(
      'SELECT * FROM property_key WHERE category_id = ?', $category['id']//spider
    );
    $keyMapper = array();
    $valueMapper = array();
    foreach ($keyList as $key) {
      $keyMapper[$key['id']] = $key;
      if ($key['is_new'] === '1') {
        Db::insert('property_key', array(//TODO check builder property_key, maybe exsits
          'category_id' => $key['category_id'], 'name' => $key['name']
        ));//builder
        $wjKeyId = Db::getLastInsertId();
        $keyMapper[$key['id']]['wj_id'] = $wjKeyId;
        $mvaIndex = null;
        if (count($keyMapper) < 101) {
          $mvaIndex = $this->getMvaIndex($categoryId);
        }
        //TODO insert key into web
      }
      $valueList = Db::getAll(
        'SELECT * FROM property_value WHERE key_id = ?', $key['id']
      );
      foreach ($valueList as $value) {
        $valueMapper[$value['id']] = $value;
        if ($value['is_new']) {
          //TODO insert into web
        }
      }
    }
    //TODO if mva index changed, save to builder category
    //$this->syncProductByCategory($category);
  }

  private $mvaIndexList;
  private $mvaIndexSearchStartPoint;
  private $isMvaIndexListModified = false;

  private function getMvaIndex($categoryId) {
    if ($this->mvaIndexList === null) {
      $column = Db::getColumn(
        'SELECT mva_index_list FROM category WHERE id = ?', $categoryId
      );
      list($mvaIndexList, $this->mvaIndexSearchStartPoint) =
        explode(';', $column, 2);
      if ((int)$this->mvaIndexSearchStartPoint === 100) {
        return;
      }
      $this->mvaIndexList = explode(',', $mvaIndexList);
    }
    for ($index = $this->mvaIndexSearchStartPoint; $index < 100; ++$index) {
      if (in_array($index, $this->mvaIndexList) === false) {
        $mvaIndexList[] = $index;
        $this->isMvaIndexListModified = true;
        $this->mvaIndexSearchStartPoint = $index + 1;
        return $index;
      }
    }
  }

  private function syncProductByCategory($category) {
    $logList = Db::getAll(
      'SELECT * FROM log WHERE category_id = ?', $category['id']
    );
    foreach ($logList as $log) {
      $class = 'Product'.ucfirst(strtolower($log['type'])).'Processor';
      $processor = new $class;
      $processor->execute(
        $log['product_id'], $this->keyMapper, $this->valueMapper
      );
      Db::delete('log', 'id = ?', $log['id']);
    }
  }
}