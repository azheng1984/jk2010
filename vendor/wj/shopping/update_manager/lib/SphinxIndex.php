<?php
class SphinxIndex {
  private static $mainDate = null;

  public static function index() {
    //TODO 构建多级索引，使用商品总数控制增量索引，用时间控制总索引更新
    $date = date('Y-m-d');
    if (self::$mainDate === null) {
      self::$mainDate = require DATA_PATH.'main_index_date.php';
    }
    self::system('indexer delta --rotate > /dev/null');
    if (self::$mainDate !== $date) {
      try {
        self::merge();
        self::system('indexer --merge main delta --rotate > /dev/null');
        file_put_contents(
          DATA_PATH.'main_index_date.php',
          '<?php return "'.$date.'";'
        );
        self::$mainDate = $date;
      } catch (Exception $ex) {
        throw $ex;
      }
    }
  }

  private static function merge() {
    DbConnection::connect('search');
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
        if ($productDelta['price_from_x_100'] === null) {
          Db::delete('product', 'id = ?', $id);
          continue;
        }
        Db::bind('product', array('id' => $id), $productDelta);
      }
      Db::delete('product_delta', 'id <= ?', $id);
    }
    DbConnection::close();
  }

  public static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      throw new Exception;
    }
  }
}