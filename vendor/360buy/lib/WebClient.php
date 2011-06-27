<?php
class WebClient {
  private $handlers = array();

  public function get($domain, $path = '/', $cookie = null) {
    $handler = $this->getHandler($domain, $path);
    curl_setopt($handler, CURLOPT_HTTPGET, true);
    return $this->execute($handler, $cookie);
  }

  public function post(
    $domain, $path = '/', $uploadData = null, $cookie = null
  ) {
    $handler = $this->getHandler($domain, $path);
    curl_setopt($handler, CURLOPT_POST, true);
    curl_setopt($handler, CURLOPT_POSTFIELDS, $uploadData);
    return $this->execute($handler, $cookie);
  }

  public function close() {
    foreach ($this->handlers as $handler) {
      curl_close($handler);
    }
    $this->handlers = array();
  }

  private function getHandler($domain, $path) {
    if (!isset($this->handlers[$domain])) {
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
      $this->handlers[$domain] = $handler;
    }
    $handler = $this->handlers[$domain];
    curl_setopt($handler, CURLOPT_URL, 'http://'.$domain.$path);
    return $handler;
  }

  private function execute($handler, $cookie) {
    curl_setopt($handler, CURLOPT_COOKIE, $cookie);
    $content = curl_exec($handler);
    $result = curl_getinfo($handler);
    $result['content'] = $content;
    return $result;
  }
}