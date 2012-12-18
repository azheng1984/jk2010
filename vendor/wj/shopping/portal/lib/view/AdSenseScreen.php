<?php
class AdSenseScreen {
  private static $isInitialized = false;
  public static function render($solt, $class = 'ad') {
    echo '<div class="', $class, '">',
      '<div class="content"><script type="text/javascript">';
    if (self::$isInitialized === false) {
      echo 'google_ad_client="pub-6400920337804269";google_ad_width=728;',
        'google_ad_height=90;';
      self::$isInitialized = true;
    }
    echo 'google_ad_slot="', $solt, '";',
      '</script>',
//      '<script type="text/javascript"',
//      'src="http://pagead2.googlesyndication.com/pagead/show_ads.js">',
//      '</script>',
        '<img src="/+/img/imgad.gif"/>',
      '</div></div>';
  }
}