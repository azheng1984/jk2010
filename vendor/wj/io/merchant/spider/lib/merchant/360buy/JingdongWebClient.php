<?php
class JingdongWebClient {
  public static function get(
    $domain, $path = '/', $headers = array(),
    $cookie = null, $returnResponseHeader = false, $retryTimes = 2
  ) {
    try {
      return WebClient::get(
        $domain, $path, $headers, $cookie, $returnResponseHeader, $retryTimes
      );
    } catch (Exception $exception) {
      $count = 0;
      for(;;) {
        try {
          WebClient::get('www.360buy.com');//测试是否断网/宕机/被阻止
        } catch (Exception $ex) {
          if ($count !== 0 && $count % 60 === 0) {
            error_log(
              'jingdong network error at '.date('Y-m-d H:i:s').'(10 min)'
            );
          }
          ++$count;
          sleep(10);
          continue;
        }
        break;
      }
      if ($count > 0) {
        return self::get(
          $domain, $path, $headers, $cookie, $returnResponseHeader, $retryTimes
        );
      }
      Db::insert('request_error_log', array(
        'url' => $domain.$path,
        'status_code' => $exception->getCode(),
        'version' => $GLOBALS['VERSION']
      ));
      throw $exception;
    }
  }
}