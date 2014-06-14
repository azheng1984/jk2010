<?php

class RequestUrl {
    public function name($param) {
        $articleId = RequestUrl::getId();
        RequestUrl::getId(0);
        RequestUrl::get();
        ActionResult::get('article');
    }
}
