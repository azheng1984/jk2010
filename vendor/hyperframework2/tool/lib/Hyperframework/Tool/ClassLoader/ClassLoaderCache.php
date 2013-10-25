<?php
class ClassLoaderCache {
    private $cache = array(array(), array());
    private $folders = array();
    private $fullPathCache = array();

    public function append($class, $fullPath, $relativeFolder, $rootFolder) {
        if (isset($this->cache[0][$class])) {
            throw new Exception(
                "Conflict class '$class':".PHP_EOL
                .$this->fullPathCache[$class].PHP_EOL.$fullPath
            );
        }
        $this->cache[0][$class] = $this->getIndex($rootFolder, $relativeFolder);
        $this->fullPathCache[$class] = $fullPath;
    }

    public function export() {
        return array('class_loader' => $this->cache);
    }

    private function getIndex($rootFolder, $relativeFolder) {
        if ($rootFolder === null && $relativeFolder === null) {
            return true;
        }
        if ($rootFolder === null) {
            return $this->getFolderIndex($relativeFolder, $relativeFolder);
        }
        $rootFolderIndex = $this->getFolderIndex($rootFolder, array($rootFolder));
        if ($relativeFolder === null) {
            return $rootFolderIndex;
        }
        return $this->getFolderIndex(
            $rootFolder.DIRECTORY_SEPARATOR.$relativeFolder,
            array($relativeFolder, $rootFolderIndex)
        );
    }

    private function getFolderIndex($path, $cache) {
        if (isset($this->folders[$path])) {
            return $this->folders[$path];
        }
        $index = count($this->cache[1]);
        $this->cache[1][$index] = $cache;
        $this->folders[$path] = $index;
        return $index;
    }
}
