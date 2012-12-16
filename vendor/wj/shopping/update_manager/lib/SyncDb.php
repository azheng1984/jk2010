<?php
//手工测试 update
class SyncDb {
  private $file;
  private $categoryId;
  private $categoryName;
  private $isRetry;

  public function execute($categoryId, $categoryName, $status) {
    $this->categoryId = $categoryId;
    $this->categoryName = $categoryName;
    $this->isRetry = $status === 'retry' ? true : false;
    $this->file = fopen(SyncFile::getCommandFilePath(), 'r');
    if ($this->file === false) {
      throw new Exception;
    }
    $command = null;
    $previousCommand = null;
    DbConnection::connect('portal');
    for (;;) {
      $command = substr(fgets($this->file), 0, -1);
      if ($command === false) {
        break;
      }
      if ($command === '') {
        continue;
      }
      if ($this->executeCommand($command) === true) {
        $previousCommand = $command;
        continue;
      }
      $this->executeCommand($previousCommand, $command);
    }
    DbConnection::close();
    fclose($this->file);
  }

  private function executeCommand($command, $id = null) {
    switch($command) {
      case 'c':
        $this->insertCategory();
        break;
      case 'k':
        $this->insertKey($id);
        break;
      case 'v':
        $this->insertValue($id);
        break;
      case 'p':
        $this->insertProduct($id);
        break;
      case 'u':
        $this->updateProcuct($id);
        break;
      case 'd':
        $this->deleteProduct($id);
        break;
      default:
        return false;
    }
    echo $command;
    return true;
  }

  public function merge() {
    DbConnection::connect('portal');
    for (;;) {
      $productList = Db::getAll('SELECT * FROM product_delta ORDER BY id LIMIT 1000');
      if (count($productList) === 0) {
        break;
      }
      $id = null;
      foreach ($productList as $productDelta) {
        $id = $productDelta['id'];
        unset($productDelta['id']);
        if ($productDelta['price_from_x_100'] === null) {
          $imagePath = Db::getColumn(
            'SELECT image_path FROM product WHERE id = ?', $id
          );
          if ($imagePath !== false) {
            $filename = IMAGE_PATH.$imagePath.'/'.$id.'.jpg';
            if (is_file($filename)) {
              unlink($filename);
            }
            Db::delete('product', 'id = ?', $id);
          }
          continue;
        }
        $product = array();
        if ($productDelta['title'] !== null) {
          $product['title'] = $product['title'];
        }
        if ($productDelta['price_from_x_100'] !== null) {
          $product['price_from_x_100'] = $productDelta['price_from_x_100'];
          $productSearch = $productDelta['price_from_x_100'];
        }
        if ($productDelta['category_name'] !== null) {
          $product['category_name'] = $productDelta['category_name'];
        }
        if ($productDelta['property_list'] !== null) {
          $product['property_list'] = $productDelta['property_list'];
        }
        Db::update('product', $product, 'id = ?', $id);
      }
      Db::delete('product_delta', 'id <= ?', $id);
    }
    DbConnection::close();
  }

  private function insertCategory() {
    if ($this->isRetry) {
      Db::bind(
        'category',
        array('id' => $this->categoryId),
        array('name' => $this->categoryName)
      );
      return;
    }
    Db::insert('category', array(
      'id' => $this->categoryId,
      'name' => $this->categoryName
    ));
  }

  private function insertKey($id) {
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    $name = substr(fgets($this->file), 0, -1);
    if ($this->isRetry) {
      Db::bind(
        'property_key',
        array('id' => $id),
        array('name' => $name)
      );
      return;
    }
    Db::insert('property_key', array('id' => $id, 'name' => $name));
  }

  private function insertValue($id) {
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    $keyId = substr(fgets($this->file), 0, -1);
    $name = substr(fgets($this->file), 0, -1);
    if ($this->isRetry) {
      Db::bind(
        'property_value',
        array('id' => $id),
        array('key_id'=>$keyId, 'name' => $name)
      );
      return;
    }
    Db::insert(
      'property_value', array('id' => $id, 'name' => $name, 'key_id'=>$keyId)
    );
  }

  private function insertProduct($id) {
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    $product = array();
    $searchDeltaProduct = array();
    $product['uri_argument_list'] = substr(fgets($this->file), 0, -1);
    $product['image_path'] = substr(fgets($this->file), 0, -1);
    $product['image_digest'] = substr(fgets($this->file), 0, -1);
    $product['title'] = substr(fgets($this->file), 0, -1);
    $product['price_from_x_100'] = substr(fgets($this->file), 0, -1);
    $searchDeltaProduct['price_from_x_100'] = $product['price_from_x_100'];
    $product['price_to_x_100'] = substr(fgets($this->file), 0, -1);
    if ($product['price_to_x_100'] === '') {
      unset($product['price_to_x_100']);
    }
    $product['category_name'] = $this->categoryName;
    $searchDeltaProduct['category_id'] = $this->categoryId;
    $propertyList = array();
    for (;;) {
      $line = substr(fgets($this->file), 0, -1);
      if ($line === '') {
        $line = substr(fgets($this->file), 0, -1);
        if ($line === '') {
          break;
        }
        $propertyList[] = '';
      }
      $propertyList[] = $line;
    }
    $product['property_list'] = implode("\n", $propertyList);
    $product['agency_name'] = substr(fgets($this->file), 0, -1);
    $searchDeltaProduct['keyword_list'] = substr(fgets($this->file), 0, -1);
    $searchDeltaProduct['value_id_list'] = substr(fgets($this->file), 0, -1);
    if ($this->isRetry) {
      var_dump($id);
      DbConnection::connect('search');
      Db::bind('product_delta', array('id' => $id), $searchDeltaProduct);
      DbConnection::close();
      Db::bind('product', array('id' => $id), $product);
      return;
    }
    DbConnection::connect('search');
    $searchDeltaProduct['id'] = $id;
    Db::insert('product_delta', array('id' => $id), $searchDeltaProduct);
    DbConnection::close();
    $product['id'] = $id;
    Db::insert('product', $product);
  }

  private function updateProcuct($id) {
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    $product = array();
    $deltaProduct = array();
    $isInSearchProductDelta = true;
    DbConnection::connect('search');
    $searchDeltaProduct = Db::getRow(
      'SELECT * FROM product_delta WHERE id = ?', $id
    );
    if ($searchDeltaProduct === false) {
      $isInSearchProductDelta = false;
      $searchDeltaProduct = Db::getRow(
        'SELECT * FROM product WHERE id = ?', $id
      );
    }
    if ($searchDeltaProduct === false) {
      die('fatal error: product not found in product search');
    }
    DbConnection::close();
    for(;;) {
      $line = substr(fgets($this->file), 0, -1);
      if ($line === '') {
        break;
      }
      switch ($line) {
        case '0':
          $product['uri_argument_list'] = substr(fgets($this->file), 0, -1);
          break;
        case '1':
          $product['image_digest'] = substr(fgets($this->file), 0, -1);
          break;
        case '2':
          $deltaProduct['title'] = substr(fgets($this->file), 0, -1);
          break;
        case '3':
          $deltaProduct['price_from_x_100'] = substr(fgets($this->file), 0, -1);
          $searchDeltaProduct = $deltaProduct['price_from_x_100'];
          break;
        case '4':
          $product['price_to_x_100'] = substr(fgets($this->file), 0, -1);
          break;
        case '5':
          $deltaProduct['category_name'] = $this->categoryName;
          break;
        case '6':
          $propertyList = array();
          for (;;) {
            $line = substr(fgets($this->file), 0, -1);
            if ($line === '') {
              $line = substr(fgets($this->file), 0, -1);
              if ($line === '') {
                break;
              }
              $propertyList[] = '';
            }
            $propertyList[] = $line;
          }
          $deltaProduct['property_list'] = implode("\n", $propertyList);
          break;
        case '7':
          $product['agency_name'] = substr(fgets($this->file), 0, -1);
          break;
        case '8':
          $searchDeltaProduct['keyword_list'] = substr(fgets($this->file), 0, -1);
          break;
        case '9':
          $searchDeltaProduct['value_id_list'] = substr(fgets($this->file), 0, -1);
          break;
      }
    }
    if (count($product) > 0) {
      Db::update('product', $product, 'id = ?', $id);
    }
    if (count($deltaProduct) > 0) {
      if ($this->isRetry) {
        Db::bind('product_delta', array('id' => $id), $deltaProduct);
      } else {
        $deltaProduct['id'] = $id;
        Db::insert('product_delta', $deltaProduct);
      }
    }
    DbConnection::connect('search');
    if ($isInSearchProductDelta) {
      Db::update('product_delta', $searchDeltaProduct, array('id' => $id));
    } else {
      Db::insert('product_delta', $searchDeltaProduct);
    }
    Db::update('product', $searchDeltaProduct, array('id' => $id));
    DbConnection::close();
  }

  private function deleteProduct($id) {
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    DbConnection::connect('search');
    $searchDeltaProduct = Db::getRow(
      'SELECT id FROM product_delta WHERE id = ?', $id
    );
    if ($searchDeltaProduct === false) {
      Db::insert('product_delta', 'id = ?', $id);
    } else {
      Db::update(
        'product_delta',
        array(
          'category_id' => null,
          'price_from_x_100' => null,
          'value_id_list' => null,
          'keyword_list' => null
        )
      );
    }
    DbConnection::close();
    Db::insert('product_delta', 'id = ?', $id);
  }
}