<?php
namespace Hyperframework\Common;

class ViewTemplate extends ViewTemplateEngine {
    public function __construct(array $model = null) {
        $includeFileFunction = function() {
            include $this->getFullPath();
        };
        parent::__construct($includeFileFunction , $model);
    }
}
