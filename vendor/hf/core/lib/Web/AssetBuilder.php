<?php
class AssetBuilder {
    public function buildAll() {
        $includePaths = self::getIncludePath();
        $excludePaths = self::getExcludePath();
        foreach ($includePaths as $includePath) {
        }
    }

    public static function name($includePath) {
        if (is_file($includePath)) {
//            if (self::isExcluded($includePath) !== false) {
//            }
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($includePath),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        try {
            foreach($files as $fullFileName => $fileSPLObject) {
                print $fullFileName . " " . $fileSPLObject->getFilename() . "\n";
            }
        }
        catch (UnexpectedValueException $e) {
            printf("Directory [%s] contained a directory we can not recurse into", $directory);
        }
        return null;
    }
}
