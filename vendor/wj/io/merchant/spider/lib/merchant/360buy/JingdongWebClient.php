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
      Db::insert('request_error_log', array(
        'url' => $domain.$path,
        'status_code' => $exception->getCode(),
        'version' => $GLOBALS['VERSION']
      ));
      throw $exception;
    }
  }
}