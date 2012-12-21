<?php
class SyncDb {
  private $file;
  private $merchantId;
  private $isRetry;

  public function execute($merchantId, $status) {
    $this->merchantId = $merchantId;
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
    //echo $command;
    return true;
  }

  public function merge() {
    DbConnection::connect('portal');
    for (;;) {
      $productList = Db::getAll(
        'SELECT * FROM product_delta ORDER BY id LIMIT 1000'
      );
      if (count($productList) === 0) {
        break;
      }
      $id = null;
      foreach ($productList as $productDelta) {
        $id = $productDelta['id'];
        unset($productDelta['id']);
        if ($productDelta['price_from_x_100'] === '-1') {
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
        Db::update('product', $product, 'id = ?', $id);
      }
      Db::delete('product_delta', 'id <= ?', $id);
    }
    DbConnection::close();
  }

  private function insertProduct($id) {
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    $product = array('merchant_id' => $this->merchantId);
    $searchDeltaProduct = array();
    $product['uri_argument_list'] = substr(fgets($this->file), 0, -1);
    $searchDeltaProduct['popularity_rank'] = substr(fgets($this->file), 0, -1);
    $product['image_path'] = substr(fgets($this->file), 0, -1);
    $product['image_digest'] = substr(fgets($this->file), 0, -1);
    $product['title'] = substr(fgets($this->file), 0, -1);
    $product['price_from_x_100'] = substr(fgets($this->file), 0, -1);
    $searchDeltaProduct['price_from_x_100'] = $product['price_from_x_100'];
    $product['price_to_x_100'] = substr(fgets($this->file), 0, -1);
    if ($product['price_to_x_100'] === '') {
      unset($product['price_to_x_100']);
    }
    $product['agency_name'] = substr(fgets($this->file), 0, -1);
    if ($product['agency_name'] === '') {
      unset($product['agency_name']);
    }
    $searchDeltaProduct['keyword_list'] = substr(fgets($this->file), 0, -1);
    if ($this->isRetry) {
      DbConnection::connect('search');
      Db::bind('product_delta', array('id' => $id), $searchDeltaProduct);
      DbConnection::close();
      Db::bind('product', array('id' => $id), $product);
      return;
    }
    DbConnection::connect('search');
    $searchDeltaProduct['id'] = $id;
    Db::insert('product_delta', $searchDeltaProduct);
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
    $searchProductReplacementColumnList = array();
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
          $searchProductReplacementColumnList['popularity_rank']
            = substr(fgets($this->file), 0, -1);
          break;
        case '2':
          $product['image_digest'] = substr(fgets($this->file), 0, -1);
          break;
        case '3':
          $deltaProduct['title'] = substr(fgets($this->file), 0, -1);
          break;
        case '4':
          $deltaProduct['price_from_x_100'] = substr(fgets($this->file), 0, -1);
          $searchProductReplacementColumnList['price_from_x_100'] = $deltaProduct['price_from_x_100'];
          break;
        case '5':
          $product['price_to_x_100'] = substr(fgets($this->file), 0, -1);
          if ($product['price_to_x_100'] === '') {
            $product['price_to_x_100'] = null;
          }
          break;
        case '6':
          $product['agency_name'] = substr(fgets($this->file), 0, -1);
          if ($product['agency_name'] === '') {
            $product['agency_name'] = null;
          }
          break;
        case '7':
          $searchProductReplacementColumnList['keyword_list'] = substr(
            fgets($this->file), 0, -1
          );
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
    if (count($searchProductReplacementColumnList) > 0) {
      DbConnection::connect('search');
      if (count($searchProductReplacementColumnList) === 3) {
        if (Db::getColumn('SELECT id FROM product_delta WHERE id = ?', $id) === false) {
          $searchProductReplacementColumnList['id'] = $id;
          Db::insert(
            'product_delta',
            $searchProductReplacementColumnList
          );
        } else {
          Db::update(
            'product_delta',
            $searchProductReplacementColumnList,
            'id = ?', $id
          );
        }
      } else {
        $searchDeltaProduct = Db::getRow(
          'SELECT id FROM product_delta WHERE id = ?', $id
        );
        if ($searchDeltaProduct !== false) {
          Db::update(
            'product_delta', $searchProductReplacementColumnList, 'id = ?', $id
          );
        } else {
          $select = array();
          if (!isset($searchProductReplacementColumnList['price_from_x_100'])) {
            $select[] = 'price_from_x_100';
          }
          if (!isset($searchProductReplacementColumnList['popularity_rank'])) {
            $select[] = 'popularity_rank';
          }
          if (!isset($searchProductReplacementColumnList['keyword_list'])) {
            $select[] = 'keyword_list';
          }
          $searchProduct = Db::getColumn(
            'SELECT '.implode(',', $select).' FROM product WHERE id = ?', $id
          );
          if ($searchProduct === false) {
            error_log('fatal error, update product not in search db id:'.$id);
            exit;
          }
          $searchProduct = array_merge(
            $searchProduct, $searchProductReplacementColumnList
          );
          $searchProduct['id'] = $id;
          Db::insert('product_delta', $searchProduct);
        }
      }
      DbConnection::close();
    }
  }

  private function deleteProduct($id) {
    if ($id === null) {
      $id = substr(fgets($this->file), 0, -1);
    }
    DbConnection::connect('search');
    Db::bind('product_delta', array('id' => $id), array(
      'price_from_x_100' => null,
      'keyword_list' => null
    ));
    DbConnection::close();
    if ($this->isRetry) {
      Db::bind(
        'product_delta',
        array('id' => $id),
        array('price_from_x_100' => -1)
      );
    } else {
      Db::insert('product_delta', array('id' => $id, 'price_from_x_100' => -1));
    }
  }
}