<?php
namespace Yxj\App\Article;

class Json {
    public function render() {
        //parent
        if ($method !== 'GET') {
            if ($message !== null) {
                echo convertToJson($message);
            }
            return;
        }
        renderEntity();
        //render entity
        echo 'article json content';
    }
}
