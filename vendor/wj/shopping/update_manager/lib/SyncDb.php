<?php
class SyncDb {
  private $file;
  private $categoryId;
  private $categoryName;
  private $isRetry;

  public function execute($categoryId, $categoryName, $isRetry) {
    $this->categoryId = $categoryId;
    $this->categoryName = $categoryName;
    $this->isRetry = $isRetry === '1' ? true : false;
    $this->file = file(SyncFile::getCommandFilePath(), 'r');
    $command = null;
    $previousCommand = null;
    DbConnection::connect('portal');
    while(feof($this->file) !== true) {
      $command = fgets($this->file);
      if ($command === '') {
        continue;
      }
      if ($this->executeCommand($command) === false) {
        $this->executeCommand($previousCommand, $command);
      }
      $previousCommand = $command;
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
    return true;
  }

  public static function merge() {
    for (;;) {
      DbConnection::connect('delta');
      $productList = Db::getAll('SELECT * FROM product ORDER BY id LIMIT 1000');
      DbConnection::close();
      if (count($productList) === 0) {
        break;
      }
      $id = null;
      foreach ($productList as $productDelta) {
        $id = $productDelta['id'];
        unset($productDelta['id']);
        if ($productDelta['price_from_x_100'] === null) {
          $imagePath = Db::getColumn('SELECT image_path FROM product WHERE id = ?', $id);
          unlink(IMAGE_PATH.$imagePath.'/'.$id.'.jpg');
          Db::delete('product', $productDelta, 'id = ?', $id);
          DbConnection::connect('search');
          Db::delete('product', $productDelta, 'id = ?', $id);
          DbConnection::close();
          continue;
        }
        $product = array(
          'id' => $productDelta['id']
        );
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
        if ($product['value_id_list'] !== null) {
          $productSearch['value_id_list'] = $productDelta['value_id_list'];
        }
        if ($product['keyword_list'] !== null) {
          $productSearch['keyword_list'] = $productDelta['keyword_list'];
        }
        if ($product['popularity_rank'] !== null) {
          $productSearch['popularity_rank'] = $productDelta['popularity_rank'];
        }
        Db::update('product', $productDelta, 'id = ?', $id);
        if ($this->isRetry) {
          DbConnection::connect('search');
          Db::bind('product', array('id' => $id), $productSearch);
          DbConnection::close();
          continue;
        }
        $productSearch['id'] = $id;
        if ($productDelta['is_new'] === '1') {
          DbConnection::connect('search');
          Db::insert('product', $productSearch);
          DbConnection::close();
          continue;
        }
        DbConnection::connect('search');
        Db::update('product', $productSearch, 'id = ?', $id);
        DbConnection::close();
      }
      DbConnection::connect('delta');
      Db::delete('product', 'id <= ?', $id);
      DbConnection::close();
    }
  }

  private function insertCategory() {
    if ($this->$isRetry) {
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
    if (id === null) {
      $id = fgets($this->file);
    }
    $name = fgets($this->file);
    if ($this->$isRetry) {
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
    if (id === null) {
      $id = fgets($this->file);
    }
    $keyId = fgets($this->file);
    $name = fgets($this->file);
    if ($this->$isRetry) {
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
      $id = fgets($this->file);
    }
    $product = array();
    $productDelta = array('is_new' => true);
    $product['uri_argument_list'] = fgets($this->file);
    $product['image_path'] = fgets($this->file);
    $product['image_digest'] = fgets($this->file);
    $product['title'] = fgets($this->file);
    $product['price_from_x_100'] = fgets($this->file);
    $productDelta['price_from_x_100'] = $product['price_from_x_100'];
    $product['price_to_x_100'] = fgets($this->file);
    if ($product['price_to_x_100'] === '') {
      unset($product['price_to_x_100']);
    }
    $product['category_name'] = $this->categoryName;
    $productDelta['category_id'] = $this->categoryId;
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
    $productDelta['keyword_list'] = fgets($this->file);
    $productDelta['value_id_list'] = fgets($this->file);
    if ($this->$isRetry) {
      DbConnection::connect('delta');
      Db::bind('product', array('id' => $id), $productDelta);
      DbConnection::close();
      Db::bind('product', array('id' => $id), $product);
      return;
    }
    DbConnection::connect('delta');
    $productDelta['id'] = $id;
    Db::insert('product', array('id' => $id), $productDelta);
    DbConnection::close();
    $product['id'] = $id;
    Db::insert('product', $product);
  }

  private function updateProcuct($id) {
    if ($id === null) {
      $id = fgets($this->file);
    }
    $product = array();
    $productDelta = array();
    $productSearch = array();
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
          $productDelta['category_name'] = $this->categoryName;
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
          break;
        case '8':
          $productDelta['keyword_list'] = fgets($this->file);
          break;
        case '9':
          $productDelta['value_id_list'] = fgets($this->file);
          break;
      }
    }
    if (count($product) > 0) {
      Db::update('product', $product, 'id = ?', $id);
    }
    if (count($productDelta) > 0) {
      if ($this->isRetry) {
        DbConnection::connect('delta');
        Db::bind('product', array('id' => $id), $productDelta);
        DbConnection::close();
        return;
      }
      $productDelta['id'] = $id;
      DbConnection::connect('delta');
      Db::insert('product', $productDelta);
      DbConnection::close();
    }
  }

  private function deleteProduct($id) {
    if (id === null) {
      $id = fgets($this->file);
    }
    DbConnection::connect('delta');
    Db::insert('product', 'id = ?', $id);
    DbConnection::close();
  }
}