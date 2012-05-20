<?php
class BuildCommand {
  private $category;
  private $keyMapper;
  private $valueMapper;
  private $mvaIndexList;
  private $mvaIndexSearchStartPoint;
  private $isMvaIndexListModified = false;

  public function execute() {
    //sync meta
    DbConnection::connect('spider');
    $categoryList = Db::getAll('SELECT * FROM category');
    foreach ($categoryList as $category) {
      $this->syncByCategory($category);
    }
    //sync product
    $this->syncProductByCategory($category);
  }

  private function syncByCategory($category) {
    $this->category = $category;
    $this->mvaIndexList = null;
    $this->mvaIndexSearchStartPoint = 0;
    $this->isMvaIndexListModified = false;
    DbConnection::connect('builder');
    $isNew = false;
    $categoryId = Db::bind(
      'category', array('name' => $category['name']), null, $isNew
    );
    $this->category['wj_id'] = $categoryId;
    if ($isNew) {
      DbConnection::connect('web');
      Db::insert('category', array(
        'id' => $categoryId, 'name' => $category['name'])
      );
    }
    DbConnection::connect('spider');
    $keyList = Db::getAll(
      'SELECT * FROM property_key WHERE category_id = ?', $category['id']
    );
    $keyMapper = array();
    $valueMapper = array();
    $wjKeyId = null;
    foreach ($keyList as $key) {
      $keyMapper[$key['id']] = $key;
      if ($key['is_new'] === '1') {
        $mvaIndex = null;
        if (count($keyMapper) < 101) {
          $mvaIndex = $this->getMvaIndex($categoryId);
        }
        DbConnection::connect('builer');
        $isNew = false;
        $wjKeyId = Db::bind('property_key', array(
          'category_id' => $key['category_id'], 'name' => $key['name']
        ), array('mva_index' => $mvaIndex), $isNew);
        if ($isNew) {
          DbConnection::connect('web');
          Db::insert('property_key', array(
            'id' => $wjKeyId, 'category_id' => $key['category_id'],
            'name' => $key['name'], 'mva_index' => $mvaIndex
          ));
        }
      }
      DbConnection::connect('spider');
      $valueList = Db::getAll(
        'SELECT * FROM property_value WHERE key_id = ?', $key['id']
      );
      if ($wjKeyId === null) {
        DbConnection::connect('builder');
        $wjKeyId = Db::getColumn(
          'SELECT id FROM property_key WHERE category_id = ? AND name = ?',
          $categoryId, $key['name']
        );
      }
      $keyMapper[$key['id']]['wj_id'] = $wjKeyId;
      foreach ($valueList as $value) {
        $valueMapper[$value['id']] = $value;
        $wjValueId = null;
        if ($value['is_new']) {
          DbConnection::connect('web');
          $wjValueId = Db::bind('property_value', array(
            'key_id' => $wjKeyId, 'name' => $value['name']
          ));
        } else {
          DbConnection::connect('web');
          $wjValueId = Db::bind('property_value', array(
            'key_id' => $wjKeyId, 'name' => $value['name']
          ));
        }
        $value['wj_id'] = $wjValueId;
      }
    }
    if ($this->isMvaIndexListModified) {
      DbConnection::connect('builder');
      Db::update('property_key', array(
        'mva_index_list',
        implode(',', $this->mvaIndexList).';'.$this->mvaIndexSearchStartPoint)
      );
    }
    $this->keyMapper = $keyMapper;
    $this->valueMapper = $valueMapper;
  }

  private function getMvaIndex($categoryId) {
    if ($this->mvaIndexList === null) {
      $column = Db::getColumn(
        'SELECT mva_index_list FROM category WHERE id = ?',
        $categoryId
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
        $log['product_id'], $category, $this->keyMapper, $this->valueMapper
      );
      Db::delete('log', 'id = ?', $log['id']);
    }
  }
}