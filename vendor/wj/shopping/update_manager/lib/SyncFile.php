<?php
//TODO:打包成一个传输文件，而不是三个
class SyncFile {
  private $fileList;

  public static function execute($task) {
    $suffix = $task['merchant_id'].'_'.$task['category_id'].$task['version'];
    $this->fileList = array(
      'portal' => $suffix.'_portal',
      'product_search' => $suffix.'_product_search'
    );
    $this->fileList['portal_remote'] = $this->fileList['portal'].'.tar.gz';
    $this->fileList['product_search_remote']
      = $this->fileList['product_search'].'.tar.gz';
    $this->fileList['image_folder_remote'] = $suffix.'_image_folder.tar.gz';
    $this->getFile($this->fileList['portal_remote']);
    system('tar -zxf '.$this->fileList['portal_remote']);
    $this->getFile($this->fileList['product_search_remote']);
    system('tar -zxf '.$this->fileList['product_search_remote']);
    $this->getFile($this->fileList['image_folder_remote']);
    system('cd '.IMAGE_PATH);
    system(
      'tar -zxf '.DATA_PATH.'sync/'.$this->fileList['image_folder_remote']
    );
    return $this->fileList;
  }

  private static function getFile($fileName) {
    $ftp = null;
    for (;;) {
      $ftp = ftp_connect("127.0.0.1");
      if ($ftp !== false) {
        break;
      }
      sleep(10);
    }
    ftp_login($ftp, "shopping_update_manager", "123456")
      or die('ftp password error');
    //TODO 计算 resumepos
    if (ftp_get($ftp, $fileName, $fileName, FTP_BINARY) === false) {
      ftp_close($ftp);
      $this->getFile();
    }
    ftp_close($ftp);
  }

  public static function finialize() {
    foreach ($this->fileList as $file) {
      unlink(DATA_PATH.'sync/'.$file);
    }
  }
}