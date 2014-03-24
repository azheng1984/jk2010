<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    protected static function getDefaultPathPrefix() {
        return DIRECTORY_SEPARATOR . 'config';
    }
}
