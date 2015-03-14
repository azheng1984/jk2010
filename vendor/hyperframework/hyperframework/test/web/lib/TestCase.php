<?php
namespace Hyperframework\Web\Test;

use Hyperframework\Common\Config;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Web\Response;
use Hyperframework\Test\TestCase as Base;

class TestCase extends Base {
    protected function setUp() {
        parent::setUp();
        Config::set('hyperframework.app_root_namespace', __NAMESPACE__);
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
    }
}
