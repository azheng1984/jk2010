<?php
class WebClient {
  private $handlers = array();

  public function execute(
    $domain, $path = '/', $method = 'GET', $uploadData = null
  ) {
    $handler = $this->getHandler($domain);
    curl_setopt($handler, CURLOPT_URL, 'http://'.$domain.$path);
    $content = curl_exec($handler);
    $result = curl_getinfo($handler);
    $result['content'] = $content;
    return $result;
  }

  public function close() {
    foreach ($this->handlers as $handler) {
      curl_close($handler);
    }
    $this->handlers = array();
  }

  private function getHandler($domain) {
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
    return $this->handlers[$domain];
  }
}