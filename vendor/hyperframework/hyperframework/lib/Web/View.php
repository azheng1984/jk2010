<?php
namespace Hyperframework\Web;

class View extends ViewTemplate {
    public function __construct(array $model = null) {
        $includeFileFunction = function() {
            include $this->getFullPath();
        };
        parent::__construct($includeFileFunction , $model);
    }
}
