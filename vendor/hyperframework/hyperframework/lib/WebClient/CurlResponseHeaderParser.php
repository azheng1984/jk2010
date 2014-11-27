<?php
namespace Hyperframework\WebClient;

use Exception;

class CurlResponseHeaderParser {
    public static function parse($handle, $rawHeaders) {
        $url = curl_getinfo($handle, CURLINFO_EFFECTIVE_URL);
        $tmp = explode('://', $url, 2);
        $protocol = strtolower($tmp[0]);
        if ($protocol === 'http'
            || $protocol === 'https'
            || $protocol === 'file'
            || $protocol === 'ftp'
        ) {
        } else {
            return [];
        }
    }
}
