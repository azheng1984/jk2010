<?php
namespace Hyperframework\Web;

class ViewTemplate extends ViewTemplateEngine {
    public function __construct(array $model = null) {
        parent::__construct(
            function() {
                include $this->getFullPath();
            },
            $model
        );
    }
}
