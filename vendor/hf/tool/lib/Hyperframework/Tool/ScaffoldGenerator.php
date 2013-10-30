<?php
namespace Hyperframework\Tool;

class ScaffoldGenerator {
    public function generate($config) {
        if (!is_array($config)) {
            $config = array($config);
        }
        $this->check($config);
        foreach ($config as $path => $content) {
            if (is_int($path)) {
                list($path, $content) = array($content, null);
            }
            if (substr($path, -1) === '/') {
                $this->generateDirectory($path, $content);
                continue;
            }
            $this->generateFile($path, $content);
        }
    }

    private function check($config) {
        foreach ($config as $path => $content) {
            if (is_int($path)) {
                $path = $content;
            }
            if (file_exists($path)) {
                throw new Exception("File '$path' existed");
            }
        }
    }

    private function generateFile($path, $content) {
        $directoryPath = dirname($path);
        if (!is_dir($directoryPath)) {
            $this->generateDirectory(dirname($path));
        }
        list($mode, $content) = $this->getFileData($content);
        file_put_contents($path, $this->getOutput($content));
        $this->changeMode($path, $mode, 0644);
    }

    private function generateDirectory($path, $mode = 0755) {
        mkdir($path, 0755, true);
        $this->changeMode($path, $mode, 0755);
    }

    private function changeMode($path, $mode, $defaultMode) {
        if ($mode !== null && $mode !== $defaultMode) {
            chmod($path, $mode);
        }
    }

    private function getFileData($content) {
        if (is_array($content) && isset($content[0]) && is_int($content[0])) {
            return array(array_shift($content), $content);
        }
        return array(0644, $content);
    }

    private function getOutput($content) {
        if (is_array($content)) {
            return implode(PHP_EOL, $content);
        }
        return $content;
    }
}
