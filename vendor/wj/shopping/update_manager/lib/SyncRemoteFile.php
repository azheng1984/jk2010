<?php
class SyncRemoteFile {
  public static function sync($task) {
    $ftp = ftp_connect("");
    ftp_login($ftp,"admin","ert456");
    echo ftp_get($ftp, "target.txt", "source.txt", FTP_ASCII);
    ftp_close($ftp);
  }
}