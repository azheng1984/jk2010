<?php

class AssetCacheUrl {
    public function get($path) {
        $result = Config::get() . $path;
        $prefix = Config::get();
        $isVersionEnabled;
        //get version
        return $result;
    }
}
