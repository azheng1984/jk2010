<?php
namespace Hyperframework\Web\Test;

use Hyperframework\Web\Controller as Base;

class ParentConstructorNotCalledController extends Base {
    public function __construct() {
    }
}
