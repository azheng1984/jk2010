<?php
namespace Hyperframework\Web\Test;

use Hyperframework\Web\Controller as Base;

class InvalidConstructorController extends Base {
    public function __construct() {
    }
}
