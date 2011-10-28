<?php
class JingDongProductParser {
  public function parse() {
    $sql = 'SELECT * FROM product';
    foreach (Db::getAll($sql) as $product) {
      $html = gzuncompress($product['html']);
      preg_match(
        '{class="Ptable">(.*?)</table>}',
        $html,
        $matches
      );
      if (isset($matches[1])) {
        $brand = null;
        $model = null;
        $color = null;
        $specTable = iconv('GBK', 'utf-8', $matches[1]);
        $sections = explode('<tr><th class="tdTitle" colspan="2">', $specTable);
        foreach ($sections as $section) {
          if ($section === '') {
            continue;
          }
          preg_match(
            '{^(.*?)</th><tr>(.*)}',
            $section,
            $matches
          );
          $sectionName = $matches['1'];
          $parentId = $this->getSectionKeyId($sectionName);
          preg_match_all(
            '{<tr><td class="tdTitle">(.*?)</td><td>(.*?)</td></tr>}',
            $matches['2'],
            $matches,
            PREG_SET_ORDER
          );
          foreach ($matches as $match) {
            $key = $match[1];
            $value = $match[2];
            if ($key === '品牌') {
              $brand = $value;
              continue;
            }
            if ($key === '型号') {
              $model = $value;
              continue;
            }
            if ($key === '颜色') {
              $color = $value;
              continue;
            }
            $this->addProductKeyValue($product['id'], $parentId, $key, $value);
          }
        }
        $this->storeProductRecognitionInfo($product['id'], $brand, $model, $color);
      }
    }
  }

  private function getSectionKeyId($sectionName) {
    $sql = "SELECT id FROM property_key WHERE `key` = ?";
    $parentId = Db::getColumn($sql, $sectionName);
    if ($parentId === false) {
      $sql = "INSERT INTO property_key(`key`) VALUES(?)";
      Db::execute($sql, $sectionName);
      $parentId = Db::getLastInsertId();
    }
    return $parentId;
  }

  private function getKeyId($key, $parentId) {
    $sql = "SELECT id FROM property_key WHERE `key` = ? and parent_id = ?";
    $id = Db::getColumn($sql, $key, $parentId);
    if ($parentId === false) {
      $sql = "INSERT INTO property_key(`key`, parent_id) VALUES(?, ?)";
      Db::execute($sql, $key, $parentId);
      $id = Db::getLastInsertId();
    }
    return $id;
  }

  private function getValueId($value, $keyId) {
    $sql = "SELECT id FROM property_value WHERE `value` = ? AND key_id = ?";
    $id = Db::getColumn($sql, $value, $keyId);
    if ($id === false) {
      $sql = "INSERT INTO property_value(`value`, key_id) VALUES(?, ?)";
      Db::execute($sql, $value, $keyId);
      $id = Db::getLastInsertId();
    }
    return $id;
  }

  private function addProductKeyValue($productId, $parentKeyId, $key, $value) {
    $keyId = $this->getKeyId($key, $parentKeyId);
    $valueId = $this->getValueId($value, $keyId);
    $sql = "SELECT id FROM product_property_value WHERE product_id = ? AND property_value_id = ?";
    $id = Db::getColumn($sql, $productId, $valueId);
    if ($id === false) {
      $sql = "INSERT INTO product_property_value(`product_id`, property_value_id) VALUES(?, ?)";
      Db::execute($sql, $productId, $valueId);
    }
  }

  private function storeProductRecognitionInfo($productId, $brand, $model, $color) {
    $sql = "INSERT INTO product_recognition_info(product_id, brand, model, color) VALUES(?, ?, ?, ?)";
    Db::execute($sql, $productId, $brand, $model, $color);
  }
}