<?php
class SphinxIndex {
  private static $mainDate = null;

  public static function indexDelta() {
    self::system('indexer delta --rotate');
  }

  public static function indexMain() {
    $date = date('Y-m-d');
    if (self::$mainDate !== $date) {
      DbConnection::connect('search');
      try {
        $recode = Db::getColumn(
          'SELECT id FROM main_index_date WHERE date = ?', $date
        );
        if ($recode === false) {
          self::merge();
          self::system('indexer main --rotate');
        }
        $recode = Db::update(
          'main_index_date', array('date' => $date), 'id = 1'
        );
      } catch (Exception $ex) {
        DbConnection::close();
        sleep(10);
        self::indexMain();
        return;
      }
      DbConnection::close();
      self::$mainDate = $date;
    }
  }

  private static function merge() {
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

  public static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      throw new Exception;
    }
  }
}