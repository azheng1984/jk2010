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
      for($count = 0;;++$count) {
        try {
          WebClient::get('www.360buy.com');//测试是否断网/宕机/被阻止
        } catch (Exception $ex) {
          sleep(10);
          if ($count !== 0 && $count % 60 === 0) {
            error_log(
              'jingdong network error at '.date('Y-m-d H:i:s').'(10 min)'
            );
          }
          continue;
        }
        return self::get(
          $domain, $path, $headers, $cookie,
          $returnResponseHeader, $retryTimes
        );
        break;
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