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
    if ($this->file === null) {
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
    $productDelta = array('is_new' => true);
    $product['uri_argument_list'] = substr(fgets($this->file), 0, -1);
    $product['image_path'] = substr(fgets($this->file), 0, -1);
    $product['image_digest'] = substr(fgets($this->file), 0, -1);
    $product['title'] = substr(fgets($this->file), 0, -1);
    $product['price_from_x_100'] = substr(fgets($this->file), 0, -1);
    $productDelta['price_from_x_100'] = $product['price_from_x_100'];
    $product['price_to_x_100'] = substr(fgets($this->file), 0, -1);
    if ($product['price_to_x_100'] === '') {
      unset($product['price_to_x_100']);
    }
    $product['category_name'] = $this->categoryName;
    $productDelta['category_id'] = $this->categoryId;
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
    $productDelta['keyword_list'] = substr(fgets($this->file), 0, -1);
    $productDelta['value_id_list'] = substr(fgets($this->file), 0, -1);
    if ($this->isRetry) {
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
      $id = substr(fgets($this->file), 0, -1);
    }
    $product = array();
    $productDelta = array();
    $productSearch = array();
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
          $productDelta['title'] = substr(fgets($this->file), 0, -1);
          break;
        case '3':
          $productDelta['price_from_x_100'] = substr(fgets($this->file), 0, -1);
          break;
        case '4':
          $product['price_to_x_100'] = substr(fgets($this->file), 0, -1);
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
          $productDelta['property_list'] = implode("\n", $propertyList);
          break;
        case '7':
          $product['agency_name'] = substr(fgets($this->file), 0, -1);
          break;
        case '8':
          $productDelta['keyword_list'] = substr(fgets($this->file), 0, -1);
          break;
        case '9':
          $productDelta['value_id_list'] = substr(fgets($this->file), 0, -1);
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
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    DbConnection::connect('delta');
    Db::insert('product', 'id = ?', $id);
    DbConnection::close();
  }
}