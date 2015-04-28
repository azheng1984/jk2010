<?php
namespace Controllers;

use Hyperframework\Web\Controller as Base;
use UnexpectedValueException;

class Controller extends Base {
    public function renderView() {
        if ($this->isViewEnabled() === false) {
            return;
        }
        header('Cache-Control: max-age=0, private, must-revalidate');
        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($this->getActionResult());
        if ($json === false) {
            throw new UnexpectedValueException('Action result is invalid.');
        }
        echo $json;
    }
}
