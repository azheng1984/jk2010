<?php

class Category extends DbActiveRecord {
    public function getName() {
        return $this->getColumn('name');
    }

    public function setName($value) {
        return $this->setColumn('name', $value);
    }
}
