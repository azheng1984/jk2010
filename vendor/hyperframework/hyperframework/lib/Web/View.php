<?php
namespace Hyperframework\Web;

class View extends ViewTemplate {
    public function __construct(array $model = null) {
        $loadFileFunction = function() {
            require $this->getFullPath();
        };
        parent::__construct($loadFileFunction , $model);
    }
}
