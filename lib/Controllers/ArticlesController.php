<?php
namespace Youxuanji\Main\Controllers;

class ArticlesController extends Controller {
    public function __construct($app) {
    }

//    public function onActionExecuting() {
//    }

//    public function onActionExecuted() {
//    }

    public function doShowAction() {
        $this->render(['json' => function() {
            return encode_json($data);
        }, 'html', 'xml' => function() {
            return $data;
        }]);
        $format = $this->getRequestFormat();
        if ($format === 'json') {
            echo ....;
            return;
            $this->renderJson(function() use ($data) {
            });
            return;
            return new JsonView(function() use ($data) {
            });
            return $this->createJsonView(function() use ($data) {
                //render callback
            });
        } elseif ($format === 'json') {
            return $this->createJsonView(function() use ($data) {
            });
            //render json data
            //return new JsonView($data);
        } elseif ($format === 'xml') {
            return new XmlView($data);
        }
    }

    public function doNewAction() {
    }

    public function doDeleteAction() {
    }

    public function doListAction() {
    }
}
