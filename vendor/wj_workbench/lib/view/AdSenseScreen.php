<?php
class AdSenseScreen {
  public static function render($isImage = false) {
    if ($_SERVER['HTTP_HOST'] !== 'huobiwanjia.com') {
      echo 'Google 提供的广告';
      return;
    }
    if ($isImage) {?>
<script type="text/javascript"><!--
google_ad_client = "pub-6400920337804269";
/* 728x90, 创建于 11-10-9 */
google_ad_slot = "2433157027";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script><?php
      return;
    }
?>
<script type="text/javascript"><!--
google_ad_client = "pub-6400920337804269";
/* 728x90, 创建于 11-10-9 */
google_ad_slot = "7589107008";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script><?php
  }

  public static function renderBigBlock() {
    if ($_SERVER['HTTP_HOST'] !== 'huobiwanjia.com') {
      echo 'Google 提供的广告';
      return;
    } ?>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-6400920337804269";
/* wj_336x280_test */
google_ad_slot = "8883448359";
google_ad_width = 336;
google_ad_height = 280;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
  <?php }
}