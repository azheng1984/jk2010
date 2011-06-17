<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.dangdang.com/");
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$header = array();
$header []= 'Accept: */*';
$header []= 'Accept-Language: zh-cn';
$header []= 'User-Agent: (compatible; bingbot/2.0 +http://www.bing.com/bingbot.htm)';
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
for ($count = 0; $count < 5; ++$count) {
  ob_start();
  curl_exec($ch);
  file_put_contents('data', ob_get_clean());
  echo '.';
}
curl_close($ch);
exit(0);