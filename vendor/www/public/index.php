<?php
namespace Hft;
print_r($GLOBALS);
exit;
$x = array('xxx' => 'x');

list($b['xxx']) = $x;
print_r($b);
exit;

isset($x['x']) ? $x['x'] : null;

$ar = $app->getActionResult();
xxx($ar, 'k1', 'k2');
$query = Extractor::run($_GET, 'query');
Extractor::run($actionResult, array('action, errors'));

isset($result['errors']['xx']);

$x['x'];
var_dump(array_column($x, 'xxx'));
exit;
class a {
    public static function hello() {
        echo 'hi';
    }
}
$b = 'Hft\a';
$b::hello();
exit;
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
