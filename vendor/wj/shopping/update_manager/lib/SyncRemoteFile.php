<?php
class SyncRemoteFile {
  public static function sync($task) {
    $conn = ftp_connect("") or $x = 2;//throw new Exception;
    ftp_login($conn,"admin","ert456");
    
    echo ftp_get($conn,"target.txt","source.txt",FTP_ASCII);
    
    ftp_close($conn);
  }
}