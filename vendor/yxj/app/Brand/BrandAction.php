<?php
namespace Yxj\App\Brand;
use Hyperframework\Db\Client as DbClient;

class BrandAction {
  /**
   * list * lsififi * main is the mos
   */
  public function get() {
  }

  public function delete() {
    DbClient::beginTransaction();
    try {
      DbClient::update('brand', array('is_active' => 0), 'id = ?', $id);
    //delete all category link
    //delete location link
    //
      DbClient::commit();
    } catch (Exception $exception) {
      DbClient::rollback();
    }
  }
}
