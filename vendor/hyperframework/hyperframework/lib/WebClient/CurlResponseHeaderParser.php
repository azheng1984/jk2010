<?php
namespace Hyperframework\WebClient;

use Exception;

//lazy parse 
class CurlResponseHeaderParser {
    public static function parse($handle, $rawHeaders) {
        //check request has been sent
        $url = curl_getinfo($handle, CURLINFO_EFFECTIVE_URL);
        $tmp = explode('://', $url, 2);
        $protocol = strtolower($tmp[0]);
        if ($protocol === 'http'
            || $protocol === 'https'
            || $protocol === 'file'
            || $protocol === 'ftp'
        ) {
            if ($rawHeaders === null) {
                throw new Exception(
                    'fail to parse response headers.'
                        . ' headerwritefunction has been overwritten'
                        . ' in other callback function.'
                );
            }
        } else {
            return [];
        }
    }
}
