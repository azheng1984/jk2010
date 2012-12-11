<?php
class SphinxIndex {
//   private static $isLocked = false;

//   public static function lock() {
//     if (self::$isLocked) {
//       return;
//     }
//     for (;;) {
//       Db::execute('LOCK TABLES indexer_lock WRITE');
//       $status = Db::getColumn(
//         "SELECT status FROM indexer_lock WHERE id = 1"
//       );
//       if ($status === 'unlock') {
//         Db::update(
//           'indexer_lock', array('status' => 'lock'), "id = 1"
//         );
//         self::$isLocked = true;
//         Db::execute('UNLOCK TABLES');
//         break;
//       }
//       Db::execute('UNLOCK TABLES');
//       sleep(10);
//     }
//   }

  public static function indexDelta() {
    self::system('indexer delta --rotate');
  }

  public static function indexMain() {
    DbConnection::connect('search');
    try {
      $recode = Db::getColumn('SELECT id FROM main_index_recode WHERE date = ?');
      if ($recode !== false) {
        self::merge();
        self::system('indexer main --rotate');
      }
    } catch (Exception $ex) {
      DbConnection::close();
      sleep(10);
      self::indexMain();
      return;
    }
    DbConnection::close();
  }

  private static function merge() {
    for (;;) {
      $productList = Db::getAll('SELECT * FROM product_delta ORDER BY id LIMIT 1000');
      if (count($productList) === 0) {
        break;
      }
      $id = null;
      foreach ($productList as $productDelta) {
        $id = $productDelta['id'];
        unset($productDelta['id']);
        if ($productDelta['price_from_x_100'] === null) {//TODO TEST return null
          Db::delete('product', 'id = ?', $id);
          continue;
        }
        $product = array(
          'price_from_x_100' => $product['price_from_x_100'],
          'keyword_list' => $product['keyword_list'],
          'value_id_list' => $product['value_id_list'],
          'popularity_rank' => $product['popularity_rank'],
        );
        Db::update('product', $product, 'id = ?', $id);
      }
      Db::delete('product_delta', 'id <= ?', $id);
    }
  }

//   public static function unlock() {
//     Db::update(
//       'indexer_lock', array('status' => 'unlock'), "id = 1"
//     );
//     self::$isLocked = false;
//   }

  public static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      Db::update(
        'indexer_status', array('status' => 'unlock'), "id = 1"
      );
      throw new Exception;
    }
  }
}