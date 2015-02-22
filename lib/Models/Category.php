<?php

class Category {
    public function getCategory() {
        if (isset($this['category']) {
            return $this['category'];
        }
    }

    public function setCategory($value) {
        $this['category'] = null;
    }

    public function getName() {
        return $this->getColumn('name');
    }

    public function setName($value) {
        return $this->setColumn('name', $value);
    }
}
