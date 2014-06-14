<?php
class ActionResult {
    final public static function getActionResult($key = null/*, ...*/) {
        if ($key === null) {
            return self::$actionResult;
        }
        $result = self::$actionResult;
        foreach (func_get_args() as $key) {
            if (isset($result[$key]) === false) {
                return;
            }
            $result = $result[$key];
        }
        return $result;
    }

    final protected static function setActionResult($value) {
        self::$actionResult = $value;
    }

    public static function reset() {
        self::$actionResult = null;
        self::$pathInfo = null;
        self::$isViewEnabled = true;
        self::$shouldRewriteRequestMethod = true;
    }


}
