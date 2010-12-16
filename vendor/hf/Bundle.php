<?php
class Bundle
{
  private static $configCache;
  private $config;

  public function __construct($path)
  {
    if (self::$configCache == null) {
      self::$configCache = require BUNDLE_CACHE_FILE;
    }

    if (!isset(self::$configCache[$path])) {
      throw new ApplicationException('path not found', new NotFoundStatus);
    }

    $this->config =& self::$configCache[$path];
  }

  public function execute($mediaType, $method = 'GET')
  {
    $action = $this->getAction($mediaType);

    if (!in_array($method, $this->config['action'][$mediaType]['method'])) {
      throw new ApplicationException('method not allowed', new MethodNotAllowedStatus);
    }

    return $action->{$method}();
  }

  private function getAction($mediaType)
  {
    if (!isset($this->config['action'][$mediaType])) {
      throw new ApplicationException('unsupported media type', new UnsupportedMediaTypeStatus);
    }

    $argument = null;
    if (isset($this->config['link'])) {
      $link = new $this->config['link']['class'];
      $argument = $link->extract();
    }

    return new $this->config['action'][$mediaType]['class']($argument);
  }
}
