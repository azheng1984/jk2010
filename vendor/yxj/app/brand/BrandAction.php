<?php
class BrandAction {
  /**
   * list * lsififi * main is the mos
   */
  public function GET() {
  
  }

  public function DELETE() {
    Db::beginTransaction();
    try {
      Db::update('brand', array('is_active' => 0), 'id = ?');
      
    //delete all category link
    //delete location link
    //
      Db::commit();
    } catch (Exception $ex) {
      Db::rollback();
    }
  }
}
