<?php
class WebClient {
  private $hanlders = array();

  public function execute($domain, $path = '/') {
    $handler = $this->getHandler($domain);
    curl_setopt($handler, CURLOPT_URL, 'http://'.$domain.$path);
    $result = curl_exec($handler);
    $code = 500;
    if ($result !== false) {
      $code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
    }
    return array('http_code' => $code, 'content' => $result);
  }

  public function close() {
    foreach ($this->hanlders as $handler) {
      curl_close($handler);
    }
    $this->hanlders = array();
  }

  private function getHandler($domain) {
    if (!isset($this->hanlders[$domain])) {
      $handler = curl_init();
      curl_setopt($handler, CURLOPT_ENCODING, 'gzip');
      curl_setopt($handler, CURLOPT_TIMEOUT, 30);
      curl_setopt($handler, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
      $header = array();
      $header []= 'Accept: */*';
      $header []= 'Accept-Language: zh-CN';
      $header []= 'User-Agent: Mozilla/5.0 '
        .'(compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)';
      curl_setopt($handler, CURLOPT_HTTPHEADER, $header);
      $this->hanlders[$domain] = $handler;
    }
    return $this->hanlders[$domain];
  }
}