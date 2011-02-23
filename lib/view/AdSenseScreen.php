<?php
class AdSenseScreen {
  private static $config;

  public function render($slot) {
    if (self::$config === null) {
      self::$config = require ROOT_PATH.'config/view/'.__CLASS__.'.config.php';
    }
    if (self::$config['display'] !== false) {
      $slot = self::$config['slot'][$slot];
      echo '<script type="text/javascript"><!-- ',"\n",
           'google_ad_client = "pub-', self::$config['id'], '";',
           'google_ad_slot = "', $slot[0], '";',
           'google_ad_width = ', $slot[1], ';',
           'google_ad_height = ', $slot[2], ';',
           '//--></script>',
           '<script type="text/javascript"',
           ' src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>';
    }
  }
}