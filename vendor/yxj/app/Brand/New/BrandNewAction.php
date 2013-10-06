<?php
class BrandNewAction {
  public function GET() {}

  public function POST() {
    if ($_POST['name'] === '') {
      $_POST['name'] = '未命名';
    }
    Db::beginTransaction();
    Db::insert('brand', array(
      'name' => $_POST['name'],
      'location_id' => $_POST['location_id'],
      'parent_id' => $_POST['parent_id'],
      'abstract' => $_POST['abstract'],
      'content' => $_POST['content'],
      'rank' => $_POST['rank'],
      'creation_time' => date('Y-m-d H:i:s'),
    ));
    $id = Db::getLastInsertId();
    foreach (explode("\r\n", $_POST['category_id_list']) as $categoryId) {
      if (Db::getColumn('SELECT COUNT(*) FROM category WHERE id = ?', $categoryId) === '0') {
        echo 'category id 错误。';
        Db::rollback();
        return;
      }
      $this->bindCategory($id, $categoryId, $_POST['rank']);
    }
    if ($_POST['location_id'] !== '') {
      if (Db::getColumn('SELECT COUNT(*) FROM location WHERE id = ?', $_POST['location_id']) === '0') {
        echo 'location id 错误。';
        Db::rollback();
        return;
      }
      $this->bindLocation($id, $_POST['location_id'], $_POST['rank']);
    }
    $logoFolderId = $this->getImageFolderId();
    $logoPath = ROOT_PATH.'public/image/'.$logoFolderId.'/'.$id.'.jpg';
    $thumb = new Imagick();
    if ($_FILES['logo']['tmp_name'] !== '' && $thumb->readImage($_FILES['logo']['tmp_name'])) {
      $thumb->resizeImage(180, 180, Imagick::FILTER_LANCZOS, 1, true);
      $thumb->stripImage();
      $thumb->setImageFormat('jpeg');
      if ($thumb->writeimage($logoPath)) {
        Db::update('brand', array('logo_folder_id' => $logoFolderId), 'id = ?', $id);
        Db::execute('UPDATE image_folder SET amount = amount + 1 WHERE id = ?', $logoFolderId);
      }
      $thumb->clear();
      $thumb->destroy();
    }
    Db::commit();
    $GLOBALS['APP']->redirect('http://dev.youxuanji.com/brand-'.$id.'/');
  }

  private function bindCategory($brandId, $categoryId, $rank) {
    Db::insert('brand_category', array('brand_id' => $brandId, 'category_id' => $categoryId, 'popularity_rank' => $rank));
    Db::execute('UPDATE category SET brand_amount = brand_amount + 1 WHERE id = ?', $categoryId);
    $parentId = Db::getColumn('SELECT parent_id FROM category WHERE id = ?', $categoryId);
    if ($parentId !== '0') {
      $this->bindCategory($brandId, $parentId, $rank);
    }
  }

  private function bindLocation($brandId, $locationId, $rank) {
    Db::insert('brand_location', array('brand_id' => $brandId, 'location_id' => $locationId, 'popularity_rank' => $rank));
    Db::execute('UPDATE location SET brand_amount = brand_amount + 1 WHERE id = ?', $locationId);
    $parentId = Db::getColumn('SELECT parent_id FROM location WHERE id = ?', $locationId);
    if ($parentId !== '0') {
      $this->bindLocation($brandId, $parentId, $rank);
    }
  }

  private function getImageFolderId() {
    $folder = Db::getRow('SELECT * FROM image_folder ORDER BY amount LIMIT 1');
    $folderId = $folder['id'];
    if ($folder['amount'] === 10000) {
      Db::insert('image_folder', array());
      $folderId = Db::getLastInsertId();
      mkdir(ROOT_PATH.'public/image/'.$folderId);
    }
    return $folderId;
  }
}