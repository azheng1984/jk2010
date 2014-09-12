<?php
namespace Hyperframework\Cli;

class Reader {
    private $arguments;
    private $length;
    private $index = 1;

    public function __construct() {
        $this->length = $_SERVER['argc'];
        $this->arguments = $_SERVER['argv'];
    }

    public function get() {
        if ($this->index < 1) {
            $this->index = 1;
        }
        if ($this->index >= $this->length) {
            return null;
        }
        return $this->arguments[$this->index];
    }

    public function moveToNext() {
        ++$this->index;
    }

    public function moveToPrevious() {
        --$this->index;
    }

    public function expand($expansion) {
        array_splice($this->arguments, $this->index, 1, $expansion);
        $this->length = count($this->arguments);
        $this->moveToPrevious();
    }
}
