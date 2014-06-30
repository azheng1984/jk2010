<?php
namespace Hft;
print_r($_SERVER);
use Hyperframework\Web\EnvironmentBuilder;
use Hyperframework\Web\Runner;

define('Hft\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init_const.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'EnvironmentBuilder.php';
EnvironmentBuilder::run(__NAMESPACE__, ROOT_PATH);
//$f = new \Hyperframework\Web\Html\FormHelper;
//$f->begin();
//$f->renderTextBox(array('id' => 'content'));
//$f->end();
//echo 'hi';

$f = new \Hyperframework\Web\Html\FormBuilder(array('content' => 'hello world'));
$f->render(
    array(
        array('type' => 'TextBox', 'id' => 'content'),
    )
);
exit;
Runner::run();
