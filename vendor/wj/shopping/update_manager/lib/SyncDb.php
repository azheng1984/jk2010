<?php
//TODO:合并传输 portal & product search 重用 price_from_x_100
class SyncDb {
  private $file;

  public static function execute($fileName, $categoryId, $categoryName) {
    $this->file = file(DATA_PATH.'sync/'.$fileName, 'r');
    while(feof($this->file) !== true) {
      $command = fgets($this->file);
      if ($command === '') {
        continue;
      }
      switch($command) {
        case 'c':
          $this->insertCategory();
          break;
        case 'k':
          $this->insertKey();
          break;
        case 'v':
          $this->insertValue();
          break;
        case 'p':
          $this->insertProduct($categoryId, $categoryName);
          break;
        case 'u':
          $this->updateProcuct($categoryId, $categoryName);
          break;
        case 'd':
          $this->deleteProduct();
          break;
      }
    }
    fclose($this->file);
  }

  public static function merge() {
    for (;;) {
      DbConnection::connect('delta');
      $productList = Db::getAll('SELECT * FROM product LIMIT 1000');
      DbConnection::close();
      if (count($productList) === 0) {
        break;
      }
      foreach ($productList as $product) {
        $id = $product['id'];
        unset($product['id']);
        if ($product['price_from_x_100'] === '0') {
          Db::delete('product', $product, 'id = ?', $id);
          $imagePath = Db::getColumn('SELECT image_path FROM product WHERE id = ?', $id);
          unlink(IMAGE_PATH.$imagePath.'/'.$id.'.jpg');
        }
        Db::update('product', $product, 'id = ?', $id);
      }
      DbConnection::connect('delta');
      Db::delete('product', 'id <= ?', $id);
      DbConnection::close();
    }
  }

  private static function insertCategory() {
    $id = fgets($this->file);
    $name = fgets($this->file);
    Db::insert('category', array('id' => $id, 'name' => $name));
  }

  private static function insertKey() {
    $id = fgets($this->file);
    $name = fgets($this->file);
    Db::insert('property_key', array('id' => $id, 'name' => $name));
  }

  private static function insertValue() {
    $id = fgets($this->file);
    $keyId = fgets($this->file);
    $name = fgets($this->file);
    Db::insert(
      'property_value', array('id' => $id, 'name' => $name, 'key_id'=>$keyId)
    );
  }

  private static function insertProduct($categoryName) {
    $product = array();
    $product['uri_argument_list'] = fgets($this->file);
    $product['image_path'] = fgets($this->file);
    $product['image_digest'] = fgets($this->file);
    $product['title'] = fgets($this->file);
    $product['price_from_x_100'] = fgets($this->file);
    $product['price_to_x_100'] = fgets($this->file);
    if ($product['price_to_x_100'] === '') {
      unset($product['price_to_x_100']);
    }
    $product['category_name'] = $categoryName;
    $propertyList = array();
    for (;;) {
      $line = fgets($this->file);
      if ($line === '') {
        break;
      }
      $propertyList[] = $line;
    }
    $product['property_list'] = implode("\n", $propertyList);
    $product['agency_name'] = fgets($this->file);
    if ($product['agency_name'] === '') {
      unset($product['agency_name']);
    }
    Db::insert('product', $product);
  }

  private static function updateProcuct() {
    $product = array();
    $productDelta = array();
    $product['id'] = fgets($this->file);
    for(;;) {
      $line = fgets($this->file);
      if ($line === '') {
        break;
      }
      switch ($line) {
        case '0':
          $product['uri_argument_list'] = fgets($this->file);
          break;
        case '1':
          $product['image_digest'] = fgets($this->file);
          break;
        case '2':
          $productDelta['title'] = fgets($this->file);
          break;
        case '3':
          $productDelta['price_from_x_100'] = fgets($this->file);
          break;
        case '4':
          $product['price_to_x_100'] = fgets($this->file);
          if ($product['price_to_x_100'] === '') {
            $product['price_to_x_100'] = null;
          }
          break;
        case '5':
          $productDelta['category_name'] = fgets($this->file);
          break;
        case '6':
          $propertyList = array();
          for (;;) {
            $line = fgets($this->file);
            if ($line === '') {
              break;
            }
            $propertyList[] = $line;
          }
          $productDelta['property_list'] = implode("\n", $propertyList);
          break;
        case '7':
          $product['agency_name'] = fgets($this->file);
          if ($product['agency_name'] === '') {
            $product['agency_name'] = null;
          }
          break;
      }
    }
    Db::update('product', $product);
    DbConnection::connect('delta');
    Db::insert('product', $productDelta);
    DbConnection::close();
  }

  private static function deleteProduct() {
    $id = fgets($this->file);
    DbConnection::connect('delta');
    Db::insert('product', 'id = ?', $id);
    DbConnection::close();
  }
}