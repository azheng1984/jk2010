<?php
namespace Hyperframework;

//psr4
//$standard = array(
//    'namespace' => '/path',
//    'namespace_2' => array('@psr0' => true, 'incude_path1', 'include_path2'),
//    'namespace/3' => array('class_name2' => 'path3'),
//    'class_name' => array('/path2'),
//    'include_path',
//    'include_path2' => array('@folder_mapping' => false),
//);

//psr4
array('Namespace\Subnamespace' => 'path');

//psr0
array('Namespace_Subnamespace' => 'path');

//include path / class map
array('path');

//bind target_path

//default root path is current app root path
array(
    'Yxj' => 'lib', //@app
    'Hyperframework' => 'vendor/hyperframework_core/lib', //@package
    'Hyperframework\News' => 'vendor/hyperframework_news/lib', //@package

    array('@root_path' => 'src', 'Namespace1', 'Namespace2'),
    '@root_path' => dirname(cwd()),
    //'@prefix' => array('Sf' => '@convert_underscore'),
    'Ns\SubNs' => 'src',
    'Namespace1',//under default root path @root/Namespace1 @has_prefix = true
    'Namespace2',
    'Ns2' => array('SubNs' => 'path'),
    'Ns4' => array('@path' => array('N1', 'N2'));
    'Ns3' => array('@append_prefix', '@path' => array('path')), //
    array('@convert_underscore', 'Ns4' => array('@ignore_namespace' => 'Child', //default
        '@path' => array('path1', 'path2'))),
    //'Ns_SubNs' => array('@convert_underscore'),
);

class ClassLoader {
    public static function run() {
        spl_autoload_register(array(__CLASS__, 'load'));
        if (class_exists(__NAMESPACE__ . '\Config')) {
        }
        //load load CacheLoader & ConfigLoader in protected method
    }

    public static function load($name) {
        require static::getPath($name);
    }

    protected static function loadConfigClass() {
    }

    private static function getPath($name) {
        if (Config::get(__CLASS__ . '\CacheEnabled')) {
            return static::getPathFromCache($name);
        }
        return ClassLocator::getPath($name);
    }

    private static function getPathFromCache($name) {
    }
}
