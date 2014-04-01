<?php
namespace Yxj;

require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';

class Runner extends Hyperframework\Web\Runner {
    public static function run($applicationPath, $configs) {
        $options = array(
            'full_name' => __CLASS__ . '.builder',
            'default' => 'Hyperframework\Web\PathInfoBuilder'
        );
        Config::get('path_info_builder', $options); 
        $configs['Hyperframework\Web\PathInfo.builder'] = 'Yxj\PathInfoBuilder';
        parent::run(__NAMESPACE__, $applicationPath, $configs);
    }

    protected static function getPath() {
        $requestPath = parent::getPath();
        //use custom router
    }

    protected static function runApplication($path) {
        //use new app type
    }

    protected static function isAsset() {
        //disable asset proxy
        return false;
    }
}
