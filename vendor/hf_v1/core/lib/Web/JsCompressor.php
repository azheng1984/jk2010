<?php
namespace Hyperframework\Web;

class JsCompressor {
    public static function compress($content) {
        $ds = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        $rc = proc_open('uglifyjs', $ds, $p);
        fwrite($p[0], $content);
        fclose($p[0]);
        $result = stream_get_contents($p[1]);
        fclose($p[1]);
        $err = stream_get_contents($p[2]);
        fclose($p[2]);
        proc_close($rc);
        return $result;
    }
}
