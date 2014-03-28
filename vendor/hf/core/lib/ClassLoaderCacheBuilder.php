<?php
class ClassLoaderCacheBuilder {
    public static function build($config, $isOneToManyMappingAllowed = true) {
        $cache = array();
        foreach ($config as $namespace => $path) {
            $segments = explode('\\', $namespace);
        }
    }
}
