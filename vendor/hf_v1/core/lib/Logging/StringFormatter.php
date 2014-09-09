<?php
namespace Hyperframework\Logging;

class StringFormatter {
    public static function format($level, $params) {
        Config::get('hyperframework.logger.type');
        if ($params[0] instanceof Closure) {
            if (count($params) > 1) {
                throw new Exception;
            }
            $callback = $params[0];
            $params = $callback();
        }
        $prefix = PHP_EOL . date('Y/m/d h:i:s') . ' [' . $level . '] ';
        if (is_array($params)) {
            if (count($params) > 1) {
                return $prefix . call_user_func_array('sprintf', $params);
            } else {
                return $prefix . $params[0];
            }
        }
        return $prefix . $params;
    }
}
