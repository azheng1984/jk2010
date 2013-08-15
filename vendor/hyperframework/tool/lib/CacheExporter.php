<?php
class CacheExporter {
    private $folder;

    public function export($result) {
        if ($result === null) {
            return;
        }
        $caches = $result;
        if (is_array($result) === false) {
            $caches = $result->export();
        }
        foreach ($caches as $name => $cache) {
            file_put_contents(
                $this->getPath($name),
                '<?php'.PHP_EOL.'return '.var_export($cache, true).';'
            );
        }
    }

    private function getPath($name) {
        if ($this->folder === null) {
            $this->folder = 'cache';
            $this->createFolder();
        }
        return $this->folder.DIRECTORY_SEPARATOR.$name.'.cache.php';
    }

    private function createFolder() {
        if (!is_dir($this->folder)) {
            mkdir($this->folder);
            chmod($this->folder, 0777);
        }
    }
}
