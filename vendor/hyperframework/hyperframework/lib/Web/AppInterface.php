<?php
namespace Hyperframework\Web;

interface AppInterface {
    /**
     * @return RouterInterface
     */
    function getRouter();
    function quit();
}